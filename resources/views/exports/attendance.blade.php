<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2, h3 { text-align: center; margin: 0; padding: 5px; }
    </style>
</head>
<body>
    <h1>{{ $school->name }}</h1>
    <h2>Laporan Presensi Kelas: {{ $classroom->name }}</h2>
    <h3>Tanggal: {{ $date }}</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Status</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student['nis'] }}</td>
                <td>{{ $student['name'] }}</td>
                <td>{{ $student['status'] }}</td>
                <td>{{ $student['check_in'] }}</td>
                <td>{{ $student['check_out'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
