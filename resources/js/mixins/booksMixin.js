export default {
    data(){
        return {
            books: []
        }
    },
    methods: {
        school_select(books){
            this.books = [];
            this.books.push({
                value: null, text: 'Selecciona una opción', disabled: true
            });
            
            books.forEach(book => {
                this.books.push({
                    value: book,
                    text: `${book.name}`
                });
            });
        }
    }
}