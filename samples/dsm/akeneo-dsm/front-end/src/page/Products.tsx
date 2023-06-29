import { useEffect, useState } from 'react';
import Header from '../component/Header';
import Table from '../component/Table';

interface Product {
    uuid: string;
    identifier: string;
    family: string;
    categories: string[];
    enabled: boolean;
}

function Products({ products, token }: { products: Product[], token: string | null }) {
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
            <Header pimInstance={pimInstance}/>
            
            {token ? ( 
                <p className='missing_token'>You haven’t an App Token yet. Please set one by following your README</p> 
                ) : (
                    <Table products={products}/>
                )
            }
        </>
    );
}

export default Products;
