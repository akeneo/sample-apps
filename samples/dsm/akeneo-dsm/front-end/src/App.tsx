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

  useEffect(() => {
    setIsLoading(true);
    fetch('http://localhost:8081/some-products', { mode: 'cors' })
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
        {
          isLoading ? (
            <p className='loader'>Loading...</p> 
          ) : ( 
            <Products products={products} token={missingAccessToken}/> 
          )
        }
      </ThemeProvider>
    </>
  );
}

export default App;
