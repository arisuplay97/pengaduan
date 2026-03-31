import React from 'react';
import { createRoot } from 'react-dom/client';
import Landing from './components/Public/Landing';
import Report from './components/Public/Report';
import Track from './components/Public/Track';

// Dynamic Rendering Based on the Root ID found in the DOM
const renderComponent = (id, Component) => {
    const el = document.getElementById(id);
    if (el) {
        const root = createRoot(el);
        // Pass any LaravelData context if available
        const rawData = window.LaravelData || {};
        root.render(<Component {...rawData} />);
    }
};

renderComponent('react-landing-root', Landing);
renderComponent('react-report-root', Report);
renderComponent('react-track-root', Track);
