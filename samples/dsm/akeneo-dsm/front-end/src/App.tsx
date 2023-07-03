import React from 'react';
import './App.css';
import { useEffect, useState } from 'react';
import { ThemeProvider } from 'styled-components';
import { connectorTheme as theme } from 'akeneo-design-system';
import Products from './page/Products';

interface Product {
  uuid: string;
  identifier: string;
  family: string;
  categories: string[];
  enabled: boolean;
}

function App() {
  const [products, setProducts] = useState<Product[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [missingAccessToken, setMissingAccessToken] = useState<string | null>(null);

  const backendUrl = process.env.REACT_APP_BACK_END_URL as string | URL;

  useEffect(() => {
    setIsLoading(true);
    fetch(backendUrl + '/some-products', { mode: 'cors' })
        .then((res) => res.json())
        .then((data) => {
            if (data.access_token) {
              setMissingAccessToken(data.access_token);
            } else {
              setProducts(data.products);
            }
            setIsLoading(false);
        })
        .catch((err) => {
            console.log('error during products retrieving : ' + err);
            setIsLoading(false);
        });
  }, []);

  return (
    <>
      <ThemeProvider theme={theme}>
        <Products products={products} token={missingAccessToken} loading={isLoading}/> 
      </ThemeProvider>
    </>
  );
}

export default App;
