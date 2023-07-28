export default {
    data() {
        return {
            school: null,
            schools: [],
        }
    },
    methods: {
        showSchools(){
            if(this.school.length > 0 && this.school !== ' '){
                axios.get('/schools/show_schools', {params: {escuela: this.school}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
    },
}