function currencyFormat(num) {
  if(num){
    if(num == 'S/D'){
      return num;
    }else{
      num = +num; 
      return (
        num
          .toFixed(2) // always two decimal digits
          .replace('.', ',') // replace decimal point character with ,
          .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
      );
    }
  }else{
    return 'N/A';
  }
}
function formatDate(dateStr) {
  if(dateStr){
    const [date, time] = dateStr.split(' ');
    const [year, month, day] = date.split('-');
    return `${day}/${month}/${year}`;
  }else{
    return 'N/A';
  }
}
function convertToDecimal(text) {
  return Number(parseFloat(text).toFixed(2));
}
function formatNumber(number) {
  return number.toLocaleString('en-US', { style: 'decimal', decimalSeparator: ',', thousandSeparator: '.' });
}
