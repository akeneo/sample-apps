import {Breadcrumb} from 'akeneo-design-system';
import logo from "../logo-my-custom-start-app.svg";

function Header({ pimInstance }: { pimInstance: string | null}) {
    return (
        <section>
            <img className='app-logo' src={logo} alt="akeneo custom app logo" />
            <div className='breadcrumb'>
                <Breadcrumb>
                <Breadcrumb.Step>
                    PRODUCT LIST
                </Breadcrumb.Step>
                <Breadcrumb.Step>
                    <a href={pimInstance ? JSON.parse(pimInstance)['pim-instance'] : '#'}>
                        {pimInstance ? JSON.parse(pimInstance)['pim-instance'].split('//')[1] : 'MY PIM INSTANCE'}
                    </a>
                </Breadcrumb.Step>
                </Breadcrumb>
            </div>
            <h2 className='dashboardTitle'>Dashboard</h2>
        </section>
    );
}

export default Header;
