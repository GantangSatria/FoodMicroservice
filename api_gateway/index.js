const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();
const PORT = 8000;

// Proxy configuration
app.use('/auth', createProxyMiddleware({ target: 'http://auth-service:8004', changeOrigin: true, pathRewrite: { '^/auth': '' } }));
app.use('/users', createProxyMiddleware({ target: 'http://user-service:8003', changeOrigin: true }));
app.use('/restaurants', createProxyMiddleware({ target: 'http://restaurant-service:8002', changeOrigin: true }));
app.use('/orders', createProxyMiddleware({ target: 'http://order-service:8001', changeOrigin: true }));
app.use('/payments', createProxyMiddleware({ target: 'http://payment-service:8005', changeOrigin: true }));

app.listen(PORT, () => {
  console.log(`API Gateway running at http://localhost:${PORT}`);
});
