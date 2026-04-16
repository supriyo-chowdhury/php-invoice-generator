<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// === Data ===
$invoice_no = 'INV-' . rand(10000, 99999);
$date = date("d-m-Y");
$customer_name = "Ashok Maity";
$customer_address = "123 Anywhere St., Any City, ST 12345";
$customer_email = "hello@reallygreatsite.com";
$product = "22 carat gold bangles";
$price = 213555.00;
$rate = 11500;
$gst_percent = 3;
$gst_amount = 288449 * $gst_percent / 100;
$cgst = $gst_amount / 2;
$sgst = $gst_amount / 2;
$net_amount = 288449 + $gst_amount;

// === Logo and Background ===
$logoData = base64_encode(file_get_contents('assets/logo.png'));
$logo_path = 'data:image/png;base64,' . $logoData;

$bg_path = base64_encode(file_get_contents('assets/invoice-bg.png'));
$bg_url = 'data:image/png;base64,' . $bg_path;

// === HTML & CSS ===
$html = '
<style>
@page { margin: 0; }
body {
  margin: 0;
  font-family: "DejaVu Sans", sans-serif;
  background-image: url("'.$bg_url.'");
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}
.wrapper {
  padding: 80px 60px 80px 60px;
  font-size: 13px;
  color: #000;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.header .logo img {
  width: 200px;
}
.header .company-info {
  text-align: right;
  font-size: 14px;
}
.section-title {
  font-size: 16px;
  font-weight: bold;
  margin: 30px 0 10px;
}
.section-title-inv {
  font-size: 24px;
  font-weight: bold;
  margin: -60px 0 30px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  font-size: 13px;
}
table th, table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}
table th {
  background-color: #f2f2f2;
}
.summary td {
  text-align: right;
}
.footer {
  margin-top: 20px;
  font-size: 12px;
  line-height: 1.5;
}
</style>

<div class="wrapper">
  <div class="header">
    <div class="logo"><img src="'.$logo_path.'" alt="Logo"></div>
    <div class="company-info">
      <strong>ELOXA GROUP</strong><br>
      DLF Cybercity, KIIT, Bhubaneswar<br>
      Kolkata, West Bengal, India – 700001<br>
      www.eloxagroup.com || info@eloxagroup.com
    </div>
  </div>

  <div class="section-title-inv" style="color: #e60000;">TAX INVOICE</div>
  
  <table>
      <tr>
        <td width="50%" style="text-align: left;">
          <strong>Invoice #:</strong> '.$invoice_no.'<br>
          <strong>Date:</strong> '.$date.'<br>
          <strong>GSTIN:</strong> 19ABCDE1234F1Z2<br>
          <strong>Company:</strong> Eloxa Group
        </td>
        <td width="50%" style="text-align: right;">
          <strong>Billed To:</strong><br>
          '.$customer_name.'<br>
          '.$customer_address.'<br>
          '.$customer_email.'
        </td>
      </tr>
    </table>


  <div class="section-title">Order Summary</div>
  <table>
    <thead>
      <tr>
        <th>Description</th>
        <th>HSN/SAC</th>
        <th>Qty</th>
        <th>Weight</th>
        <th>Rate</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>'.$product.'</td>
        <td>998313</td>
        <td>1</td>
        <td>18.573</td>
        <td>₹'.number_format($rate, 2).'</td>
        <td>₹'.number_format($price, 2).'</td>
      </tr>
      
    </tbody>
  </table>

  <table class="summary">
    <tr>
      <td colspan="4">Making Charge:</td>
      <td>₹'.number_format(74744, 2).'</td>
    </tr>
    <tr>
      <td colspan="4">Hallmark Charge:</td>
      <td>₹'.number_format(150, 2).'</td>
    </tr>
    <tr>
      <td colspan="4"><strong>Taxable Amount:</strong></td>
      <td>₹'.number_format(288449, 2).'</td>
    </tr>
    <tr>
      <td colspan="4">CGST ('.($gst_percent/2).'%)</td>
      <td>₹'.number_format($cgst, 2).'</td>
    </tr>
    <tr>
      <td colspan="4">SGST ('.($gst_percent/2).'%)</td>
      <td>₹'.number_format($sgst, 2).'</td>
    </tr>
    <tr>
      <td colspan="4"><strong>Total GST</strong></td>
      <td><strong>₹'.number_format($gst_amount, 2).'</strong></td>
    </tr>
    <tr>
      <td colspan="4"><strong>Grand Total</strong></td>
      <td><strong>₹'.number_format($net_amount, 2).'</strong></td>
    </tr>
  </table>

  

  <div class="section-title">Terms & Conditions</div>
  <div class="footer">
    Gold price is calculated as per the live market rate at the time of billing. 
    Items once sold cannot be returned; exchange is allowed as per current market value with original invoice. 
    Taxes, making charges and stone charges are non-refundable.
  </div>
</div>
';

// === Render PDF ===
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$file = 'invoices/' . $invoice_no . '.pdf';
file_put_contents($file, $dompdf->output());

echo "Invoice created successfully: <a href='$file' target='_blank'>Download PDF</a>";


?>
