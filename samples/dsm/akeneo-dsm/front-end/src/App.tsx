import React from 'react';
import './App.css';
import { ThemeProvider } from 'styled-components';
import { Badge, connectorTheme as theme } from 'akeneo-design-system';

function App() {
  return (
    <>
      <ThemeProvider theme={theme}>
      <div>
        <Badge level="primary">Hello Word!</Badge>
      </div>
      </ThemeProvider>
    </>
  );
}

export default App;
