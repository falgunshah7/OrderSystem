<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function index()
    {
        return view('order');
    }

    public function getGrid(Request $request)
    {
        $rows = $request->row_number;
        $columns = $request->column_number;
        $getOrder = Order::with('items')->where('row',$rows)->where('column',$columns)->first();

        $html = '<table class="table table-borderless">';
        $html .= '<tbody>';
        for ($i=1;$i<=$rows;$i++){
            $html .= '<tr>';
                for ($j=1;$j<=$columns;$j++){
                    if($getOrder){
                        foreach ($getOrder->items as $item){
                            if($i == $item->item_row && $j==$item->item_column){
                                $html .= '<td><button class="btn btn-primary px-5" onclick="crateOrder(this)" data-row="' . $i . '" data-col="' . $j . '" data-name="'.$item->name.'" data-price="'.$item->price.'">'.$item->name.'</button></td>';
                            }
                            else{
                                $html .= '<td><button class="btn btn-primary px-5" onclick="crateOrder(this)" data-row="' . $i . '" data-col="' . $j . '">+</button></td>';
                            }
                        }
                    }
                    else {
                        $html .= '<td><button class="btn btn-primary px-5" onclick="crateOrder(this)" data-row="' . $i . '" data-col="' . $j . '">+</button></td>';
                    }
                }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        Session::put('row_num',$rows);
        Session::put('col_num',$columns);

        return response()->json(['data' => $html]);
    }

    public function store(Request $request)
    {
        $rows = Session::get('row_num');
        $colms = Session::get('col_num');
        $getOrder = Order::where('row',$rows)->where('column',$colms)->first();
        $orderID = '';
        $order = new Order();
        if(empty($getOrder)) {
            $order->row = $rows;
            $order->column = $colms;
            $order->save();

            $orderID = $order->id;
        }
        else{
            $orderID = $getOrder->id;
        }

        $getItem = Item::where('order_id',$orderID)->where('item_row',$request->row)->where('item_column',$request->col)->first();

        $responseData = [];
        if($getItem){
            $getItem->name = $request->name;
            $getItem->price = $request->price;
            if($getItem->save()){
                $responseData = $getItem;
            }
        }
        else{
            $item = new Item();
            $item->order_id = $orderID;
            $item->name = $request->name;
            $item->price = $request->price;
            $item->item_row = $request->row;
            $item->item_column = $request->col;
            if($item->save()){
                $responseData = $item;
            }
        }

        return response()->json(['status' => 'success','data' => $responseData]);
    }
}
