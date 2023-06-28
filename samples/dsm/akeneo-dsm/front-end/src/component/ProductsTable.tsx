import { useEffect, useState } from 'react';
import { ThemeProvider } from 'styled-components';
import { Badge, 
        connectorTheme as theme,
        Breadcrumb,
        Table } from 'akeneo-design-system';
import logo from "../logo-my-custom-start-app.svg";

interface Product {
    uuid: string;
    identifier: string;
    family: string;
    categories: string[];
    enabled: boolean;
}

function ProductsTable({ products }: { products: Product[]}) {
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
                <section>
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
                </section>
                

                <section className='products-table'>
                    <Table>
                        <Table.Header sticky={0}>
                            <Table.HeaderCell>
                                Uuid
                            </Table.HeaderCell>
                            <Table.HeaderCell>
                                Identifier
                            </Table.HeaderCell>
                            <Table.HeaderCell>
                                Family
                            </Table.HeaderCell>
                            <Table.HeaderCell>
                                Categories
                            </Table.HeaderCell>
                            <Table.HeaderCell>
                                Status
                            </Table.HeaderCell>
                        </Table.Header>
                        <Table.Body>
                            {products.map(product => (
                                <Table.Row>
                                <Table.Cell>
                                    {product.uuid}
                                </Table.Cell>
                                <Table.Cell>
                                    {product.identifier}
                                </Table.Cell>
                                <Table.Cell>
                                    {product.family}
                                </Table.Cell>
                                <Table.Cell>
                                    <ul>
                                        {product.categories.map(category => (
                                            <li>{category}</li>
                                        ))}
                                    </ul>
                                </Table.Cell>
                                <Table.Cell>
                                    {
                                        product.enabled ? (
                                        <Badge level="primary">
                                            ENABLED
                                        </Badge> ) : (
                                        <Badge level="warning">
                                            WARNING
                                        </Badge> )
                                    }
                                </Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                </section>
            </ThemeProvider>
        </>
    );
}

export default ProductsTable;
