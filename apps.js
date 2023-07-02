const express = require('express');
const cors = require('cors');
const app = express();
const predictStockPrice = require('./routes/pred_lstm');

app.use(cors()); // Tambahkan middleware CORS di sini

app.use('/run_predictions', predictStockPrice);

const port = 3000;
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
