<?php 
$newCustomers = $this->getData('newCustomers');  
$lostCustomers = $this->getData('lostCustomers');  
?>

<h1>Dentist - New/Lost Customers (16, 61, 62, 63)</h1>

<a download="DentistNewLostCustomers.xlsx" href="<?= \phpOMS\Uri\UriFactory::build('{/base}/{/}?export=excel'); ?>">Export to Excel</a>

<h2>New Customers</h2>

<?php foreach($newCustomers as $rep => $customers) : ?>
<table>
	<caption><?= $rep; ?></caption>
	<thead>
		<tr>
			<th>Kd.</th>
			<th>Name</th>
			<th>Tel.</th>
			<th>Str.</th>
			<th>PLZ</th>
			<th>Ort</th>
			<th>Beleg</th>
			<th>Datum</th>
			<th>Art.</th>
			<th>Art.</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($customers as $customer) : ?>
			<tr>
				<td><?= $customer['KUNDENNUMMER']; ?></td>
				<td><?= $customer['KD_Name1']; ?></td>
				<td><?= $customer['KD_Telefon']; ?></td>
				<td><?= $customer['KD_Strasse']; ?></td>
				<td><?= $customer['KD_PLZ']; ?></td>
				<td><?= $customer['KD_Ort']; ?></td>
				<td><?= $customer['BELEGNUMMER']; ?></td>
				<td><?= (new \DateTime($customer['BELEGDATUM']))->format('Y-m-d'); ?></td>
				<td><?= $customer['ARTIKELNUMMER']; ?></td>
				<td><?= $customer['ArtikelBez1']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endforeach; ?>

<h2>Lost Customers</h2>

<?php foreach($lostCustomers as $rep => $customers) : ?>
<table>
	<caption><?= $rep; ?></caption>
	<thead>
		<tr>
			<th>Kd.</th>
			<th>Name</th>
			<th>Tel.</th>
			<th>Str.</th>
			<th>PLZ</th>
			<th>Ort</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($customers as $customer) : ?>
			<tr>
				<td><?= $customer['KUNDENNUMMER']; ?></td>
				<td><?= $customer['KD_Name1']; ?></td>
				<td><?= $customer['KD_Telefon']; ?></td>
				<td><?= $customer['KD_Strasse']; ?></td>
				<td><?= $customer['KD_PLZ']; ?></td>
				<td><?= $customer['KD_Ort']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endforeach; ?>