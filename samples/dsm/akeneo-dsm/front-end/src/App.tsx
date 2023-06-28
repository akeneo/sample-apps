import React from 'react';
import './App.css';
import { useEffect, useState } from 'react';
import { ThemeProvider } from 'styled-components';
import { connectorTheme as theme } from 'akeneo-design-system';
import ProductsTable from './component/ProductsTable';

interface Product {
  uuid: string;
  identifier: string;
  family: string;
  categories: string[];
  enabled: boolean;
}

function App() {
  const [products, setProducts] = useState<Product[]>([]);

  useEffect(() => {
    fetch('http://localhost:8081/some-products', { mode: 'cors' })
        .then((res) => res.json())
        .then((data) => {
            setProducts(data.products);
        })
        .catch((err) => {
            console.log('error : ' + err);
        });
  }, []);

  return (
    <>
      <ThemeProvider theme={theme}>
        <ProductsTable products={products} />
      </ThemeProvider>
    </>
  );
}

export default App;
