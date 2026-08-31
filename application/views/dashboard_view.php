<div class="row" style="margin-bottom:20px;">
	<div class="col-xs-12">
		<h2>Dashboard</h2>
	</div>
</div>

<!-- Informasi Pengguna -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Informasi Pengguna</strong></div>
			<div class="panel-body">
				<p><strong>Nama Lengkap:</strong> <?php echo isset($current_user['nama_lengkap']) ? $current_user['nama_lengkap'] : '-'; ?>
				</p>
				<p><strong>Username:</strong> <?php echo isset($current_user['username']) ? $current_user['username'] : '-'; ?>
				</p>
				<p><strong>Role:</strong> <?php echo isset($current_user['role_name']) ? ucfirst($current_user['role_name']) : '-'; ?>
				</p>
				<p><strong>Perusahaan:</strong> <?php echo isset($current_user['nama_perusahaan']) ? $current_user['nama_perusahaan'] : '-'; ?>
				</p>
			</div>
		</div>
	</div>
</div>

<!-- Summary Fitur -->
<div class="row">
	<!-- Total Akun -->
	<div class="col-md-3">
		<div class="panel panel-info">
			<div class="panel-heading">Total Akun</div>
			<div class="panel-body text-center">
				<h3>
					<?php echo isset($total_akun) ? $total_akun : 0; ?>
				</h3>
			</div>
			<div class="panel-footer text-center">
				<a href="<?php echo site_url('akun'); ?>">Lihat Detail</a>
			</div>
		</div>
	</div>

	<!-- Total Jurnal Transaksi -->
	<div class="col-md-3">
		<div class="panel panel-success">
			<div class="panel-heading">Total Jurnal Transaksi</div>
			<div class="panel-body text-center">
				<h3>
					<?php echo isset($total_jurnal) ? $total_jurnal : 0; ?>
				</h3>
			</div>
			<div class="panel-footer text-center">
				<a href="<?php echo site_url('jurnal'); ?>">Lihat Detail</a>
			</div>
		</div>
	</div>

	<!-- Buku Besar -->
	<div class="col-md-3">
		<div class="panel panel-warning">
			<div class="panel-heading">Buku Besar</div>
			<div class="panel-body text-center">
				<h3>Link</h3>
			</div>
			<div class="panel-footer text-center">
				<a href="<?php echo site_url('buku_besar'); ?>">Lihat Detail</a>
			</div>
		</div>
	</div>

	<!-- Laporan & Laba Rugi -->
	<div class="col-md-3">
		<div class="panel panel-danger">
			<div class="panel-heading">Laporan / Laba Rugi</div>
			<div class="panel-body text-center">
				<h3>Link</h3>
			</div>
			<div class="panel-footer text-center">
				<a href="<?php echo site_url('laporan'); ?>">Lihat Detail</a>
			</div>
		</div>
	</div>
</div>
