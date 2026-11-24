<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class OrderItem extends Model
{
    protected static string $table = 'order_items';
    protected static array $fillable = ['order_id', 'menu_item_id', 'item_name', 'qty', 'price', 'subtotal'];
}
