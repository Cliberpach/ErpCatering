<table class="table table-hover table-bordered" id="table_orden_compra_to_orden_pago_montos">
    <tbody>
      <tr>
        <th scope="row">SUBTOTAL</th>
        <td id="tbl_subtotal">
            {{ $orden_compra->moneda === 'PEN' ? 'S/' : '$' }}
            {{ number_format($orden_compra->subtotal, 2) }}
        </td>        
      </tr>
      <tr>
        <th scope="row">IGV 18%</th>
        <td id="tbl_monto_igv">
            {{ $orden_compra->moneda === 'PEN' ? 'S/' : '$' }}
            {{ number_format($orden_compra->monto_igv, 2) }}   
        </td> 
      </tr>
      <tr>
        <th scope="row">TOTAL</th>
        <td id="tbl_total">
            {{ $orden_compra->moneda === 'PEN' ? 'S/' : '$' }}
            {{ number_format($orden_compra->total, 2) }}     
        </td> 
      </tr>
    </tbody>
</table>