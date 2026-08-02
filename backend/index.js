require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const apiRoutes = require('./routes/api');
const db = require('./database/db'); // Ensures DB is initialized

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(helmet()); // Security headers
app.use(cors()); // Allow frontend communication
app.use(express.json({ limit: '50mb' })); // Parse JSON bodies with increased limit for images

// Routes
app.use('/api', apiRoutes);

// Basic health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'ok', message: 'FraudEye API is running' });
});

// Start Server
app.listen(PORT, () => {
    console.log(`FraudEye Backend running on http://localhost:${PORT}`);
});
