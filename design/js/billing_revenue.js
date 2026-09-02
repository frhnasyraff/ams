$(document).ready(function () {
  let sum = $('#revenue_total').text();
  sum = parseFloat(sum.replace(/,/g, ''));
  $('.disposal_unit_price').change(function () {
    update_amounts(sum);
  });
});

function update_amounts(tot) {
  let sum_Value = 0;
  $('.revenue_cal').each(function (i, e) {
    if ($('.disposal_unit_price_' + i).length == 1) {
      let unitpri = 'disposal_unit_price_' + i;
      let price = $('input[type=number][name=' + unitpri + ']').val();
      let qty = $('.disposal_qty_' + i).text();
      let amount = parseFloat(qty) * parseFloat(price);
      sum_Value += amount;

      $('.form_disposal_total_' + i).val(amount.toLocaleString('en', { useGrouping: false, minimumFractionDigits: 2 }));
    }
  });
  tot += sum_Value;
  $('#revenue_total').text(tot.toLocaleString());
}
