<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Payment;
use Carbon\Carbon;
use DB;
use PDF;
use App\OrderReturn;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::withCount(['return'])->where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('ecommerce.orders.index', compact('orders'));
    }

    public function view($invoice)
    {
        $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
            ->where('invoice', $invoice)->first();

        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.view', compact('order'));
            }
        }else {
            return redirect()->back();
        }    
        
        return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Order Orang Lain']);
    }

    public function paymentForm($invoice)
    {
        $order = Order::with([ 'payment'])->where('invoice', $invoice)->first();
        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.payment', compact('order'));
            }
        }else {
            return redirect()->back();
        }  

        return redirect()->back()->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Payment Order Orang Lain']);
    }

    public function storePayment(Request $request)
    {
        $this->validate($request, [
            'invoice' => 'required|exists:orders,invoice',
            'name' => 'required|string',
            'transfer_to' => 'required|string',
            'transfer_date' => 'required',
            'amount' => 'required|integer',
            'proof' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        //DEFINE DATABASE TRANSACTION UNTUK MENGHINDARI KESALAHAN SINKRONISASI DATA JIKA TERJADI ERROR DITENGAH PROSES QUERY
        DB::beginTransaction();
        try {
            $order = Order::where('invoice', $request->invoice)->first();
            
            if ($order->total != $request->amount) return redirect()->back()->with(['error' => 'Error, Pembayaran Harus Sama Dengan Tagihan']);

            if ($order->status == 0 && $request->hasFile('proof')) {
                $file = $request->file('proof');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/payment', $filename);

                Payment::create([
                    'order_id' => $order->id,
                    'name' => $request->name,
                    'transfer_to' => $request->transfer_to,
                    'transfer_date' => Carbon::parse($request->transfer_date)->format('Y-m-d'),
                    'amount' => $request->amount,
                    'proof' => $filename,
                    'status' => false
                ]);
                
                $order->update(['status' => 1]);
                //JIKA TIDAK ADA ERROR, MAKA COMMIT UNTUK MENANDAKAN BAHWA TRANSAKSI BERHASIL
                DB::commit();
                return redirect()->route('customer.view_order', $order->invoice)->with(['success' => 'Pesanan Dikonfirmasi']);
            }
            return redirect()->back()->with(['error' => 'Error, Upload Bukti Transfer']);
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function pdf($invoice) 
    {
        $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
                ->where('invoice', $invoice)->first();
        if(Order::where('invoice', $invoice)->exists()) {
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
                $pdf = PDF::loadView('ecommerce.orders.pdf', compact('order'));
                $filename = $order->invoice;
                return $pdf->download($filename.'-invoice.pdf');
            }else {
                return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Invoice Orang Lain']);
            }
        } else {
            return redirect(route('customer.orders'))->with(['error' => 'Invoice Tidak ada dalam Orderan Anda']);
        }
    }

    public function acceptOrder(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
            return redirect()->back()->with(['error' => 'Bukan Pesanan Kamu']);
        }
        //pesanan diterima
        $order->update(['status' => 4]);
        return redirect()->back()->with(['success' => 'Pesanan Dikonfirmasi']);
    }

    public function returnForm($invoice)
    {
        $order = Order::where('invoice', $invoice)->first();
        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.return', compact('order'));
            }
        }else {
            return redirect()->back();
        }  

        return redirect()->back()->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Return Order Orang Lain']);
    }

    public function processReturn(Request $request, $id)
    {
        $this->validate($request, [
            'reason' => 'required|string',
            'refund_transfer' => 'required|string',
            'photo' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        $return = OrderReturn::where('order_id', $id)->first();
        if ($return) return redirect()->back()->with(['error' => 'Permintaan Refund Dalam Proses']);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/returns', $filename);

            OrderReturn::create([
                'order_id' => $id,
                'photo' => $filename,
                'reason' => $request->reason,
                'refund_transfer' => $request->refund_transfer,
                'status' => 0
            ]);

            $order = Order::find($id); 
            //kirim pesan return
            $this->sendMessage($order->invoice, $request->reason); 

            return redirect()->route('customer.orders')->with(['success' => 'Permintaan Refund Dikirim']);
        }
    }

    //Curl Telegram
    private function getTelegram($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $params); 

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode($content, true);
    }

    private function sendMessage($invoice, $reason)
    {
        $key = env('TELEGRAM_KEY'); 

        $chat = $this->getTelegram('https://api.telegram.org/'. $key .'/getUpdates', '');

        if ($chat['ok']) {
            //cukup ambil key 0 atau admin saja untuk mendapatkan chat_id
            $chat_id = $chat['result'][0]['message']['chat']['id'];

            $text = 'Hai Admin E-Commerce, OrderID '.$invoice.' Melakukan Permintaan Refund Dengan Alasan "'. $reason.'", Silahkan Segera Dicek Ya!';
        
            //kirim request ke telegram untuk mengirim pesan
            return $this->getTelegram('https://api.telegram.org/'. $key .'/sendMessage', '?chat_id=' . $chat_id . '&text=' . $text);
        }
    }

}
