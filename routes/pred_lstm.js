const express = require('express');
const lstm = require('../model/lstm_models');

const router = express.Router();

// Middleware untuk mem-parse body request dalam format JSON
router.use(express.json());

router.get('/', async (req, res) => {
  console.log('HELLO');
  res.json({ message: 'Hello from /run_predictions' });
});

router.post('/run', async (req, res) => {
  try {
    let pred_param = req.body;
    console.log(pred_param);
    const predictionData = await lstm.predictStockPrice(pred_param.mode);
    res.json(predictionData);
  } catch (error) {
    console.log(error);
    res.status(500).json({ error: 'Failed to retrieve prediction data' });
  }
});

module.exports = router;
