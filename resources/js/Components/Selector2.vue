<template>

    <div class="   w-100">


        <!--                             dropdown data               -->
        <div :id="'hDropdown'+refId" class="dropdown-content ">
            <div class="input-group   center-block ">
                <div class="input-group-prepend   btn-group-vertical p-1">
                    <i class="fa fa-search text-primary  "></i>
                </div>
                <input type="text" :placeholder="placeholder" v-model="sData" :id="'input_qr_text_'+refId"
                       class="my-1 py-1 pr-1 form-control border"
                       @focus="openDropdown('h')"
                       @click="openDropdown('h')"
                       @blur="closeDropdown('h')"
                       @keypress.enter="closeDropdown('h')"
                       @keyup="selectData(sData,'key')">
                <div class=" input-group-append  btn-group-vertical   ">
                    <i class=" glyphicon glyphicon-remove text-danger  clear-btn"
                       @click="sData=''; selectData(sData,'clr');$root.$emit('search')"></i>
                </div>
            </div>

            <ul class="list-group mb-5  hide" ref="listItems" :id="'list-data-docs-'+refId">
                <li v-for="h  in   this.filteredData" class="list-group-item  data-items"
                    :id="refId+h['id']" :ref="refId+h['id']" :key="h['id']"
                    :class="{'active': selected && selected.filter(e=>e.name===h['name']).length>0}"
                    @mousedown.prevent="sData='';selectData(h,h['id'])">
                    {{ h['name'] }}
                </li>
            </ul>
        </div>
    </div>

</template>

<script>
let selectedBefore = false;
let selected = '';
export default {
    props: ['dataLink', 'for', 'multi', 'beforeSelected', 'refId', 'placeholder'],
    data() {
        return {
            sData: this.data ? this.data : '',

            data_dropdown: null,
            province_input: null,

            filteredData: [],
            data: [],
            selected: [],
            activeData: [false],
            offset: -1, // in multi=false همه نمایندگی ها not exist
            backspace: false,
            params: [], //send dropdown params for search
        }
    },

    computed: {},


    mounted() {
//            console.log('drop');
        this.data_dropdown = document.querySelector('#list-data-docs-' + this.refId);
        this.data_input = document.getElementById('input_qr_text_' + this.refId);


        this.getData();
//************* data parameters

        this.data_input.addEventListener('keydown', (e) => {
            if (e.keyCode === 8) {
                this.backspace = true;
                this.searchCounty = '';
                this.filteredCounties = [];
                this.openDropdown('h');
            } else {
                this.backspace = false;
            }
        });
    },
    updated() {
//            this.getData();
        if (!this.data) {
            this.getData();
            console.log(this.data);
        }

//            console.log(this.filteredData);
    },
    created() {

    },
    methods: {


        getData() {
            axios.get(this.dataLink, {})
                .then((response) => {
//                        console.log(response);
                    this.data = response.data;
                    this.filteredData = this.data;

                }).catch((error) => {
                console.log(' error:');
                console.log(error);
            });
        },
        openDropdown(el) {
            if (el === 'h') {
                this.filteredData = this.data;
//                    console.log(this.data);
                this.data_dropdown.classList.remove('hide');

            }
        },
        closeDropdown(el) {

            if (el === 'h') {

                this.filteredData = this.data;
                let i = 0;
                if (i < 4) {
                    this.sData = '';
                    for (let i in this.selected) {
                        this.sData += this.selected[i].name + ', ';
                    }
                    this.sData = this.sData.slice(0, this.sData.length - 2); //remove last ,
//                            console.log(this.sData);

                }
                this.data_dropdown.classList.add('hide');
            }
        },
        beforeSelect(beforeSelected) {
            if (!beforeSelected) return;
            this.selected = [beforeSelected];
            this.closeDropdown('h');
        },
        selectData(h, hId) {
            if (hId === 'clr') {
//                    all.classList.remove('active');
                document.querySelector('list-data-docs-' + this.refId + ' .data-items').classList.remove('active');
//                    for (let i in this.activeData) {
//                        this.activeData[i] = false;
//                    }
                this.selected = [];
            } else if (hId === 'key') {

                this.filteredData = this.data.filter(entry => {
                    return entry['name'].includes(this.sData);
                });
//                    if (this.multi && this.filteredData[0]['name'].includes('همه'))
//                        this.filteredData.shift(); //remove first item (همه نمایندگی ها)
                if (this.sData === '' || this.sData === ' ')
                    this.filteredData = this.data;
                if (this.filteredData.length === 0)
                    this.filteredData = this.data;
            } else {

                this.selected = [];
                let item = document.querySelector('#' + this.refId + hId);
                if (!this.multi) {

                    document.querySelectorAll('#list-data-docs-' + this.refId + ' .data-items').forEach((el, idx) => {
                        el.classList.remove('active')
                    });
                    document.querySelector('#input_qr_text_' + this.refId).blur();
                }
                item.classList.toggle('active');
                for (let i = 0; i < this.data.length; i++)
                    if (document.getElementById(this.refId + this.data[i].id).classList.contains('active')
                    ) this.selected.push(this.data[i]);


                if (!this.multi) {
//                        $("#dataInput").blur();
                    this.closeDropdown('h');
                }

            }
            this.$root.$emit('dropdown_click', {
                'group_id': this.selected.length > 0 ? this.selected[0].id : 1,
                'for': this.refId
            });
        },

    }

}


</script>
