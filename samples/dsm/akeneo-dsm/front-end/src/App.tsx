import React from 'react';
import './App.css';
import { useEffect, useState } from 'react';
import { ThemeProvider } from 'styled-components';
import logo from "./logo-my-custom-start-app.svg";
import { Badge, 
        connectorTheme as theme,
        Breadcrumb } from 'akeneo-design-system';

function App() {
  const [pimInstance, setPimInstance] = useState<string | null>(null);

  useEffect(() => {
    fetch('http://localhost:8081/my-pim-instance', { mode: 'cors' })
        .then((res) => res.json())
        .then((data) => {
            setPimInstance(JSON.stringify(data));
        })
        .catch((err) => {
            console.log('error : ' + err);
        });
  }, []);

  return (
    <>
      <ThemeProvider theme={theme}>
        <img className='app-logo' src={logo} alt="akeneo custom app logo" />
        <div className='breadcrumb'>
          <Breadcrumb>
            <Breadcrumb.Step>
              PRODUCT LIST
            </Breadcrumb.Step>
            <Breadcrumb.Step href={pimInstance ?? '#'}>
              {pimInstance ? JSON.parse(pimInstance)['pim-instance'] : 'MY PIM INSTANCE'}
            </Breadcrumb.Step>
          </Breadcrumb>
        </div>

        <h2 className='dashboardTitle'>Dashboard</h2>
      </ThemeProvider>
    </>
  );
}

export default App;
