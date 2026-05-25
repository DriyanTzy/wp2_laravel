<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Web Prog II | Merancang Template sederhana dengan Codeigniter</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/stylebuku.css') }}">
</head>

<body>
     <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div class="judul">
                <h1>RentalBuku.net</h1>
                <p>Template Sederhana dengan CodeIgniter</p>
            </div>

            <div class="menu">

            </div>
        </div>
         

        <!-- CONTENT -->
        <div class="content">
            <h2>Halaman Home</h2>

            <table>
                <tr>
                    <th width="20%">Istilah</th>
                    <th>Penjelasan</th>
                </tr>

                <tr>
                    <td>Model</td>
                    <td>
                        Model adalah kelas yang merepresentasikan atau memodelkan 
                        tipe data yang akan digunakan oleh aplikasi. 
                        Model digunakan untuk pengolahan database seperti 
                        mengambil, menginput, dan mengubah data.
                    </td>
                </tr>

                <tr>
                    <td>View</td>
                    <td>
                        View merupakan bagian yang menangani tampilan 
                        user interface atau halaman website yang tampil di browser.
                    </td>
                </tr>

                <tr>
                    <td>Controller</td>
                    <td>
                        Controller merupakan penghubung antara model dan view. 
                        Controller bertugas mengatur alur aplikasi dan memproses instruksi.
                    </td>
                </tr>
            </table>
        </div>


        <!-- FOOTER -->
        <div class="footer">
             <a href="http://www.RentalBuku.com">RentalBuku</a>
        </div>
        

    </div>
</body>
</html>