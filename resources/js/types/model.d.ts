interface Item {
    id: number;
    name: string;
    price: number;
    memo: string | null;
    is_selling: boolean;
}

interface Customer {
    id: number;
    name: string;
    kana: string;
    tel: number;
    email: string;
    postcode: string;
    address: string;
    birthday: string | null;
    gender: number | null;
    memo: string | null;
}