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

function Products({ products, token, loading }: { products: Product[], token: string | null, loading: boolean }) {
    const [pimInstance, setPimInstance] = useState<string | null>(null);

    const backendUrl = process.env.REACT_APP_BACK_END_URL as string | URL;

    useEffect(() => {
        fetch(backendUrl + '/my-pim-instance', { mode: 'cors' })
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
                <p className='missing_token'>You haven’t an App Token yet. Please set one by following our README</p> 
                ) : (
                    loading ? <p className='loader'>Loading...</p> : <Table products={products}/>
                )
            }
        </>
    );
}

export default Products;
