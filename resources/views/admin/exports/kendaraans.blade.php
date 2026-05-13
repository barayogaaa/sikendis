<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; vertical-align: top; }
        th { background: #e2e8f0; font-weight: bold; }
        .text { mso-number-format: "\@"; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>OPD</th>
                <th>Plat Nomor</th>
                <th>Merk</th>
                <th>Tipe</th>
                <th>Tahun</th>
                <th>Nomor Rangka</th>
                <th>Nomor Mesin</th>
                <th>Nomor BPKB</th>
                <th>Tanggal STNK</th>
                <th>Pengguna / Penanggung Jawab</th>
                <th>NIP Pengguna / Penanggung Jawab</th>
                <th>Status Verifikasi</th>
                <th>Catatan Admin</th>
                <th>Diinput Oleh</th>
                <th>Tanggal Submit</th>
                <th>Diverifikasi Oleh</th>
                <th>Tanggal Verifikasi</th>
                <th>Riwayat Nopol</th>
                <th>Scan BPKB</th>
                <th>Scan STNK</th>
                <th>Foto Kendaraan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kendaraans as $kendaraan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kendaraan->opd?->nama }}</td>
                    <td class="text">{{ $kendaraan->plat_nomor }}</td>
                    <td>{{ $kendaraan->merk }}</td>
                    <td>{{ $kendaraan->tipe }}</td>
                    <td>{{ $kendaraan->tahun }}</td>
                    <td class="text">{{ $kendaraan->nomor_rangka }}</td>
                    <td class="text">{{ $kendaraan->nomor_mesin }}</td>
                    <td class="text">{{ $kendaraan->nomor_bpkb }}</td>
                    <td>{{ $kendaraan->tanggal_stnk?->format('d/m/Y') }}</td>
                    <td>{{ $kendaraan->pengguna_penanggung_jawab }}</td>
                    <td class="text">{{ $kendaraan->nip_pengguna_penanggung_jawab }}</td>
                    <td>{{ $kendaraan->status_label }}</td>
                    <td>{{ $kendaraan->catatan_admin }}</td>
                    <td>{{ $kendaraan->creator?->name }}</td>
                    <td>{{ $kendaraan->submitted_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $kendaraan->verifier?->name }}</td>
                    <td>{{ $kendaraan->verified_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        @foreach ($kendaraan->riwayatPlatNomors as $riwayat)
                            {{ $riwayat->plat_nomor_lama }} ke {{ $riwayat->plat_nomor_baru }}
                            @if ($riwayat->tanggal_perubahan)
                                ({{ $riwayat->tanggal_perubahan->format('d/m/Y') }})
                            @endif
                            @if ($riwayat->keterangan)
                                - {{ $riwayat->keterangan }}
                            @endif
                            @if (! $loop->last)
                                <br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $kendaraan->scan_bpkb ? asset('storage/'.$kendaraan->scan_bpkb) : '' }}</td>
                    <td>{{ $kendaraan->scan_stnk ? asset('storage/'.$kendaraan->scan_stnk) : '' }}</td>
                    <td>{{ $kendaraan->foto_kendaraan ? asset('storage/'.$kendaraan->foto_kendaraan) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
