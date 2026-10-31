import { Header } from '@/components/Header';
import { WelcomeProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

export default function Terms() {
    const { auth, institution } = usePage<WelcomeProps>().props;

    return (
        <>
            <Head title="Términos del servicio">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />
        </>
    );
}
