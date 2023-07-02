const readline = require('readline');
const fs = require('fs');
const tf = require('@tensorflow/tfjs-node');
const axios = require('axios');
const moment = require('moment');
const mysql = require('mysql');
const path = require('path');

// Fungsi utama
async function predictStockPrice(mode) {
  
  let getDataPredictions
  if (mode === 'getFromAPI') {
   getDataPredictions =  await fetchDataFromAPI();
  } else if (mode === 'getFromDatabase') {
    getDataPredictions =  await fetchDataFromDatabase();
  } else {
    console.log('Invalid mode. Please choose either "getFromAPI" or "getFromDatabase".');
  }
  // console.log(getDataPredictions)
  return getDataPredictions
}

// Fungsi untuk mengambil data dari API
async function fetchDataFromAPI() {
  const apiUrl = 'http://api.marketstack.com/v1/eod';
  const accessKey = 'fee5343bdd33d5d2cd91c406abe00b62';
  const symbols = 'BBCA.XIDX';
  const dateFrom = '2023-05-01';
  const dateTo = '2023-05-25';

  try {
    const response = await axios.get(apiUrl, {
      params: {
        access_key: accessKey,
        symbols: symbols,
        date_from: dateFrom,
        date_to: dateTo
      }
    });

    const dataAPI = response.data.data;
    if (dataAPI) {
      console.log('Data has been fetched from API.');
    } else {
      console.log('Error accessing API.');
      return null; // Mengembalikan null jika terjadi error
    }

    const data = processData(dataAPI);
    return trainAndPredict(data); // Mengembalikan hasil prediksi
  } catch (error) {
    console.log('Error:', error.message);
    return null; // Mengembalikan null jika terjadi error
  }
}

// Fungsi untuk mengambil data dari database MySQL
async function fetchDataFromDatabase() {
  const host = 'localhost';
  const user = 'root';
  const password = '';
  const database = 'lstm';
  const sqlQuery = 'SELECT * FROM adro ORDER BY no ASC LIMIT 25';

  try {
    const connection = mysql.createConnection({
      host: host,
      user: user,
      password: password,
      database: database
    });

    connection.connect();

    return new Promise((resolve, reject) => {
      connection.query(sqlQuery, async function (error, results, fields) {
        if (error) {
          console.log('Error accessing database:', error.message);
          connection.end();
          reject(null); // Mengembalikan null jika terjadi error
          return;
        }

        const data = processData(results);
        const predictions = await trainAndPredict(data); // Mendapatkan hasil prediksi

        connection.end();
        resolve(predictions); // Mengembalikan hasil prediksi
      });
    });
  } catch (error) {
    console.log('Error connecting to database:', error.message);
    return null; // Mengembalikan null jika terjadi error
  }
}

// Fungsi untuk memproses data
// Fungsi untuk memproses data
function processData(dataAPI) {
  const data = dataAPI.map((item) => {
    const dateObj = moment(item.Date, 'DD/MM/YYYY').format('YYYY-MM-DD');
    return {
      open: parseFloat(item.Open),
      high: parseFloat(item.High),
      low: parseFloat(item.Low),
      close: parseFloat(item.Close),
      date: dateObj.substring(0, 10)
    };
  });

  return data;
}
// Fungsi untuk melatih model dan melakukan prediksi
async function trainAndPredict(data) {
  const numDays = data.length;
  const trainingData = [];
  const targetOpenData = [];
  const targetHighData = [];
  const targetLowData = [];
  const targetCloseData = [];

  data.forEach((row) => {
    const { open, high, low, close } = row;
    trainingData.push([open, high, low]);
    targetOpenData.push(open);
    targetHighData.push(high);
    targetLowData.push(low);
    targetCloseData.push(close);
  });

  const xs = tf.tensor2d(trainingData);
  const ysOpen = tf.tensor2d(targetOpenData, [numDays, 1]);
  const ysHigh = tf.tensor2d(targetHighData, [numDays, 1]);
  const ysLow = tf.tensor2d(targetLowData, [numDays, 1]);
  const ysClose = tf.tensor2d(targetCloseData, [numDays, 1]);

  let modelOpen;
  let modelHigh;
  let modelLow;
  let modelClose;

  const modelOpenPath = './training_models/modelOpen/model.json';
  const modelHighPath = './training_models/modelHigh/model.json';
  const modelLowPath = './training_models/modelLow/model.json';
  const modelClosePath = './training_models/modelClose/model.json';

  if (
    fs.existsSync(modelOpenPath + '/model.json') &&
    fs.existsSync(modelHighPath + '/model.json') &&
    fs.existsSync(modelLowPath + '/model.json') &&
    fs.existsSync(modelClosePath + '/model.json')
  ) {
    modelOpen = await tf.loadLayersModel('file://' + path.resolve(modelOpenPath + '/model.json'));
    modelHigh = await tf.loadLayersModel('file://' + path.resolve(modelHighPath + '/model.json'));
    modelLow = await tf.loadLayersModel('file://' + path.resolve(modelLowPath + '/model.json'));
    modelClose = await tf.loadLayersModel('file://' + path.resolve(modelClosePath + '/model.json'));
  } else {
    modelOpen = tf.sequential();
    modelOpen.add(tf.layers.dense({ units: 64, inputShape: [3], activation: 'relu' }));
    modelOpen.add(tf.layers.dense({ units: 1 }));
    modelOpen.compile({ loss: 'meanSquaredError', optimizer: 'adam' });

    modelHigh = tf.sequential();
    modelHigh.add(tf.layers.dense({ units: 64, inputShape: [3], activation: 'relu' }));
    modelHigh.add(tf.layers.dense({ units: 1 }));
    modelHigh.compile({ loss: 'meanSquaredError', optimizer: 'adam' });

    modelLow = tf.sequential();
    modelLow.add(tf.layers.dense({ units: 64, inputShape: [3], activation: 'relu' }));
    modelLow.add(tf.layers.dense({ units: 1 }));
    modelLow.compile({ loss: 'meanSquaredError', optimizer: 'adam' });

    modelClose = tf.sequential();
    modelClose.add(tf.layers.dense({ units: 64, inputShape: [3], activation: 'relu' }));
    modelClose.add(tf.layers.dense({ units: 1 }));
    modelClose.compile({ loss: 'meanSquaredError', optimizer: 'adam' });

    await modelOpen.fit(xs, ysOpen, {
      epochs: 100,
      verbose: 0
    });

    await modelHigh.fit(xs, ysHigh, {
      epochs: 100,
      verbose: 0
    });

    await modelLow.fit(xs, ysLow, {
      epochs: 100,
      verbose: 0
    });

    await modelClose.fit(xs, ysClose, {
      epochs: 100,
      verbose: 0
    });

    // Simpan model ke dalam folder "./training_models"
    if (!fs.existsSync(modelOpenPath)) {
      fs.mkdirSync(modelOpenPath, { recursive: true });
    }
    if (!fs.existsSync(modelHighPath)) {
      fs.mkdirSync(modelHighPath, { recursive: true });
    }
    if (!fs.existsSync(modelLowPath)) {
      fs.mkdirSync(modelLowPath, { recursive: true });
    }
    if (!fs.existsSync(modelClosePath)) {
      fs.mkdirSync(modelClosePath, { recursive: true });
    }

    await modelOpen.save('file://' + path.resolve(modelOpenPath));
    await modelHigh.save('file://' + path.resolve(modelHighPath));
    await modelLow.save('file://' + path.resolve(modelLowPath));
    await modelClose.save('file://' + path.resolve(modelClosePath));
  }

  console.log('\nLatihan model selesai.');

  const predictionData = [];

  let nextOpen = data[data.length - 1].open;
  let nextHigh = data[data.length - 1].high;
  let nextLow = data[data.length - 1].low;
  let nextClose = data[data.length - 1].close;

  for (let i = 1; i <= numDays; i++) {
    const predictionOpen = modelOpen.predict(tf.tensor2d([[nextOpen, nextHigh, nextLow]]));
    const predictionHigh = modelHigh.predict(tf.tensor2d([[nextOpen, nextHigh, nextLow]]));
    const predictionLow = modelLow.predict(tf.tensor2d([[nextOpen, nextHigh, nextLow]]));
    const predictionClose = modelClose.predict(tf.tensor2d([[nextOpen, nextHigh, nextLow]]));

    const predictedOpen = Array.from(predictionOpen.dataSync())[0];
    const predictedHigh = Array.from(predictionHigh.dataSync())[0];
    const predictedLow = Array.from(predictionLow.dataSync())[0];
    const predictedClose = Array.from(predictionClose.dataSync())[0];

    const latestData = data[data.length - 1];
    const differenceOpen = (predictedOpen - nextOpen).toFixed(2);
    const differenceHigh = (predictedHigh - nextHigh).toFixed(2);
    const differenceLow = (predictedLow - nextLow).toFixed(2);
    const differenceClose = (predictedClose - nextClose).toFixed(2);

    const percentageDifferenceOpen = (((predictedOpen - nextOpen) / nextOpen) * 100).toFixed(2);
    const percentageDifferenceHigh = (((predictedHigh - nextHigh) / nextHigh) * 100).toFixed(2);
    const percentageDifferenceLow = (((predictedLow - nextLow) / nextLow) * 100).toFixed(2);
    const percentageDifferenceClose = (((predictedClose - nextClose) / nextClose) * 100).toFixed(2);

    const predictionObj = {
      date: getNextDate(latestData.date, i),
      actual_open: nextOpen,
      actual_high: nextHigh,
      actual_low: nextLow,
      actual_close: nextClose,
      prediction_open: predictedOpen,
      prediction_high: predictedHigh,
      prediction_low: predictedLow,
      prediction_close: predictedClose,
      difference_open: differenceOpen,
      difference_high: differenceHigh,
      difference_low: differenceLow,
      difference_close: differenceClose,
      percentage_difference_open: percentageDifferenceOpen,
      percentage_difference_high: percentageDifferenceHigh,
      percentage_difference_low: percentageDifferenceLow,
      percentage_difference_close: percentageDifferenceClose
    };

    predictionData.push(predictionObj);

    nextOpen = predictedOpen;
    nextHigh = predictedHigh;
    nextLow = predictedLow;
    nextClose = predictedClose;
  }

  return predictionData;
}






// Fungsi untuk mendapatkan daftar tanggal (hari mendatang)
function getNextDate(date, days) {
  const currentDate = moment(date);
  const nextDate = currentDate.add(days, 'days');
  return nextDate.format('YYYY-MM-DD');
}
module.exports = {
  predictStockPrice
};
