import React from 'react';
import { createRoot } from 'react-dom/client';
import Login from './components/Login';

const rootElement = document.getElementById('react-login-root');

if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<Login />);
}
