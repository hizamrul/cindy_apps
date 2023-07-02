<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <title>Prediksi Harga Saham LQ45</title>
  <style>
    /* Custom CSS untuk mempercantik tampilan */
    body {
      padding-top: 70px;
      font-family: Arial, sans-serif;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .container-fluid {
      max-width: 1200px;
    }

    .sidebar {
      height: calc(100vh - 56px);
      overflow-y: auto;
      background-color: #f8f9fa;
      border-right: 1px solid #dee2e6;
    }

    .sidebar-item {
      cursor: pointer;
      font-size: 14px;
    }

    .sidebar-item:hover {
      background-color: #e9ecef;
    }

    #lineChart {
      height: 400px;
      margin-bottom: 20px;
    }

    .table thead th {
      font-size: 14px;
      font-weight: bold;
      text-align: center;
      background-color: #f8f9fa;
      border-top: none;
    }

    .table td {
      font-size: 14px;
      vertical-align: middle;
      text-align: center;
    }

    .subrow {
	  display: none;
	  background-color: white;
	  transition: all 0.3s ease;
	}

	.subrow.show {
	  display: table-row;
	}


    .subrow td {
      padding: 10px;
      font-size: 12px;
    }

    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }
	


  </style>
</head>

<body>
  <!-- Navbar Menu -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Prediksi Saham LQ45</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar Panel -->
        <div class="sidebar">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Perusahaan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Konfigurasi koneksi database
                $host = 'localhost'; // Ganti dengan nama host Anda
                $user = 'root'; // Ganti dengan nama pengguna MySQL Anda
                $pass = ''; // Ganti dengan kata sandi MySQL Anda
                $db   = 'lstm'; // Ganti dengan nama database Anda

                // Membuat koneksi ke database
                $conn = new mysqli($host, $user, $pass, $db);

                // Memeriksa koneksi
                if ($conn->connect_error) {
                    die("Koneksi database gagal: " . $conn->connect_error);
                }

                // Query untuk mengambil data dari tabel daftar_perusahaan
                $sql = "SELECT * FROM daftar_perusahaan";
                $result = $conn->query($sql);

                // Memeriksa apakah query berhasil dieksekusi
                if ($result === false) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                // Memeriksa apakah ada data yang diambil
                if ($result->num_rows > 0) {
                    // Menampilkan data per baris
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='sidebar-item'>";
                        echo "<td>" . $row["kode"] . "</td>";
                        echo "<td>" . $row["nama_perusahaan"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr>";
                    echo "<td colspan='2'>Tidak ada data yang ditemukan.</td>";
                    echo "</tr>";
                }

                // Menutup koneksi database
                $conn->close();
                ?>
            </tbody>
        </table>
      </div>
      <div class="col-md-9">
      	

        <!-- Line Chart -->
        <canvas id="lineChart"></canvas>
        <!-- Tabel Data -->
        <div class="mt-4">
          <table class="table table-hover" id="data-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Open</th>
                <th>Close</th>
                <th>High</th>
                <th>Low</th>
                <th>Prediksi Open</th>
                <th>Prediksi Close</th>
                <th>Prediksi High</th>
                <th>Prediksi Low</th>
              </tr>
            </thead>
            <tbody>
              <!-- Tambahkan data perbandingan lainnya -->
            </tbody>
          </table>
        </div>
        <!-- Card Berita -->
		<div class="card mb-4">
		  <div class="card-header">
		    <h5 class="card-title">Berita Saham Terkini</h5>
		  </div>
		  <div class="card-body">
		  <div id="news-table">
		    <!-- Berita akan ditambahkan secara dinamis -->
		  </div>
		</div>

		</div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1"></script>
  <?php 

  		include 'script.php';

   ?>
  <script>
    // Fungsi untuk menghasilkan data acak sebagai sampel
    function generateData() {
      var data = [];
      var startDate = new Date('2023-01-01');
      for (var i = 0; i < 6; i++) {
        var date = new Date(startDate.getTime() + i * 24 * 60 * 60 * 1000);
        var price = Math.floor(Math.random() * (120 - 95 + 1) + 95); // Harga acak antara 95 dan 120
        var prediction = price + 2; // Prediksi acak (ditambah 2 sebagai contoh)
        data.push({
          date: date.toLocaleDateString(),
          open: price,
          close: price + 1,
          high: price + 2,
          low: price - 1,
          predOpen: prediction,
          predClose: prediction + 1,
          predHigh: prediction + 2,
          predLow: prediction - 1
        });
      }
      return data;
    }

    // Fungsi untuk menampilkan data, tabel, dan grafik berdasarkan perusahaan yang dipilih
	function displayCompanyData(company) {
	  var data = generateData(); // Menghasilkan data acak sebagai sampel
	  var table = document.getElementById('data-table');
	  var tbody = table.getElementsByTagName('tbody')[0];

	  // Menghapus data sebelumnya dari tabel
	  while (tbody.firstChild) {
	    tbody.removeChild(tbody.firstChild);
	  }

	  // Mengisi tabel dengan data perbandingan
	  for (var i = 0; i < data.length; i++) {
	    var row = document.createElement('tr');
	   row.addEventListener('click', function() {
		  var subrow = this.nextElementSibling;
		  if (subrow) {
		    subrow.classList.toggle('show');
		  }
		});

	    row.innerHTML = `
	      <td>${data[i].date}</td>
	      <td>${data[i].open}</td>
	      <td>${data[i].close}</td>
	      <td>${data[i].high}</td>
	      <td>${data[i].low}</td>
	      <td>${data[i].predOpen}</td>
	      <td>${data[i].predClose}</td>
	      <td>${data[i].predHigh}</td>
	      <td>${data[i].predLow}</td>
	    `;
	    tbody.appendChild(row);

	    // Tambahkan baris subrow untuk menampilkan selisih dan persentase selisih
	    var subrow = document.createElement('tr');
	    subrow.classList.add('subrow');
	    subrow.innerHTML = `
	      <td colspan="9">
	        <table class="table table-sm mb-0">
	          <tbody>
	            <tr>
	              <th></th>
	              <th>Harga Asli</th>
	              <th>Harga Prediksi</th>
	              <th>Selisih</th>
	              <th>Persentase Selisih</th>
	            </tr>
	            <tr>
	              <th>Open</th>
	              <td>${data[i].open}</td>
	              <td>${data[i].predOpen}</td>
	              <td>${data[i].predOpen - data[i].open}</td>
	              <td>${(((data[i].predOpen - data[i].open) / data[i].open) * 100).toFixed(2)}%</td>
	            </tr>
	            <tr>
	              <th>Close</th>
	              <td>${data[i].close}</td>
	              <td>${data[i].predClose}</td>
	              <td>${data[i].predClose - data[i].close}</td>
	              <td>${(((data[i].predClose - data[i].close) / data[i].close) * 100).toFixed(2)}%</td>
	            </tr>
	            <tr>
	              <th>High</th>
	              <td>${data[i].high}</td>
	              <td>${data[i].predHigh}</td>
	              <td>${data[i].predHigh - data[i].high}</td>
	              <td>${(((data[i].predHigh - data[i].high) / data[i].high) * 100).toFixed(2)}%</td>
	            </tr>
	            <tr>
	              <th>Low</th>
	              <td>${data[i].low}</td>
	              <td>${data[i].predLow}</td>
	              <td>${data[i].predLow - data[i].low}</td>
	              <td>${(((data[i].predLow - data[i].low) / data[i].low) * 100).toFixed(2)}%</td>
	            </tr>
	          </tbody>
	        </table>
	      </td>
	    `;
	    tbody.appendChild(subrow);
	  }

	  // Menghapus data sebelumnya dari grafik
	  if (window.lineChart && typeof window.lineChart.destroy === 'function') {
	    window.lineChart.destroy();
	  }

	  // Menambahkan data ke grafik
	  var ctx = document.getElementById('lineChart').getContext('2d');
	  window.lineChart = new Chart(ctx, {
	    type: 'line',
	    data: {
	      labels: data.map(function(item) {
	        return item.date;
	      }),
	      datasets: [
	        {
	          label: 'Harga Asli',
	          data: data.map(function(item) {
	            return item.open;
	          }),
	          borderColor: 'blue',
	          fill: false
	        },
	        {
	          label: 'Harga Prediksi',
	          data: data.map(function(item) {
	            return item.predOpen;
	          }),
	          borderColor: 'red',
	          fill: false
	        }
	      ]
	    },
	    options: {
	      responsive: true,
	      interaction: {
	        intersect: false,
	        mode: 'index'
	      },
	      plugins: {
	        title: {
	          display: true,
	          text: 'Prediksi Harga Saham LQ45',
	          fontSize: 16,
	          fontColor: '#333',
	          fontStyle: 'bold',
	          padding: {
	            top: 20,
	            bottom: 0
	          }
	        },
	        tooltip: {
	          mode: 'index',
	          intersect: false
	        },
	        legend: {
	          display: true,
	          position: 'bottom',
	          labels: {
	            font: {
	              size: 12
	            }
	          }
	        }
	      },
	      scales: {
	        x: {
	          display: true,
	          title: {
	            display: true,
	            text: 'Date',
	            font: {
	              size: 14,
	              weight: 'bold'
	            }
	          },
	          ticks: {
	            font: {
	              size: 12
	            }
	          }
	        },
	        y: {
	          display: true,
	          title: {
	            display: true,
	            text: 'Harga Saham',
	            font: {
	              size: 14,
	              weight: 'bold'
	            }
	          },
	          ticks: {
	            font: {
	              size: 12
	            }
	          }
	        }
	      }
	    }
	  });
	}



    // Event handler untuk item di sidebar
    var sidebarItems = document.getElementsByClassName('sidebar-item');
    for (var i = 0; i < sidebarItems.length; i++) {
      sidebarItems[i].addEventListener('click', function() {
        var company = this.cells[1].textContent;
        displayCompanyData(company);
      });
    }

    // Menampilkan data dan grafik untuk perusahaan pertama saat pertama kali halaman dimuat
    var firstCompany = document.getElementsByClassName('sidebar-item')[0].cells[1].textContent;
    displayCompanyData(firstCompany);
	// Fungsi untuk mengambil berita dari API dan menampilkan dalam bentuk tabel
		function getNews() {
		  var apiKey = '67b654f7c80a422c8687e041c11c4316';
		  var apiUrl = 'https://newsapi.org/v2/top-headlines?country=id&apiKey=' + apiKey;

		  fetch(apiUrl)
		    .then(response => response.json())
		    .then(data => {
		      var newsTable = document.getElementById('news-table');
		      newsTable.innerHTML = '';

		      var news = data.articles;
		      var newsCount = news.length;
		      var table = document.createElement('table');
		      table.classList.add('table');
		      var tbody = document.createElement('tbody');

		      for (var i = 0; i < newsCount; i += 3) {
		        var row = document.createElement('tr');

		        for (var j = i; j < i + 3 && j < newsCount; j++) {
		          var article = news[j];
		          var cell = document.createElement('td');
		          cell.style.textAlign = 'left';

		          var link = document.createElement('a');
		          link.href = article.url;
		          link.target = '_blank';
		          link.textContent = article.title.length > 70 ? article.title.substring(0, 70) + '...' : article.title;

		          var image = document.createElement('img');
		          image.src = article.urlToImage || 'https://png.pngtree.com/png-vector/20190820/ourmid/pngtree-no-image-vector-illustration-isolated-png-image_1694547.jpg';
		          image.alt = 'Article Image';
		          image.style.width = '100%';
		          image.style.objectFit = 'cover';
		          image.style.height = '150px';
		          image.style.marginTop = '10px';

		          cell.appendChild(link);
		          cell.appendChild(image);
		          row.appendChild(cell);
		        }

		        tbody.appendChild(row);
		      }

		      table.appendChild(tbody);
		      newsTable.appendChild(table);
		    })
		    .catch(error => {
		      console.error('Error:', error);
		    });
		}

		// Memuat berita saat halaman dimuat
		getNews();
		setInterval(getNews, 5000);







  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>
</body>

</html>
