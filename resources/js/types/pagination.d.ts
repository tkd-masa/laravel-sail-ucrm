interface Link {
    active: boolean;
    label: string;
    url: string;
}

interface Pagination<Model> {
    current_pate: number;
    data: Array<Model>;
    first_page_url: string;
    from: number;
    last_page: number;
    past_page_url: string;
    links: Array<Link>;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}