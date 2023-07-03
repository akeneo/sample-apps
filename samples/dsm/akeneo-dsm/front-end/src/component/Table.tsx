import { Badge,
    Table } from 'akeneo-design-system';
import { Product } from '../page/Products';

function ProductsTable({ products }: { products: Product[]}) {
    return (
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
    )
}

export default ProductsTable;
