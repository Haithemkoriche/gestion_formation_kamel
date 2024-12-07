<script setup>
import { useI18n } from 'vue-i18n'
const { t, locale } = useI18n()
</script>
<template>
  <v-container>
    <h2>{{$t('Gestion des Formations')}}</h2>

    <VDataTable :headers="headers" :items="formations" class="mb-5" item-value="id" dense>
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>{{$t('Formations')}}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="openCreateDialog">{{$t('Ajouter Formation')}}</v-btn>
        </v-toolbar>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-btn icon color="blue" @click="openEditDialog(item)">
          <v-icon>tabler-pencil</v-icon>
        </v-btn>
        <v-btn icon color="red" @click="deleteformation(item.id)">
          <v-icon>tabler-trash</v-icon>
        </v-btn>
      </template>
    </VDataTable>

    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span v-if="editMode">{{$t('Modifier Formation')}}</span>
          <span v-else>{{$t('Ajouter Formation')}}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form">
            <VTextField class="mb-4" v-model="form.name" :label="$t('Nom de la Formation')" required></VTextField>
            <v-textarea class="mb-4" v-model="form.description" :label="$t('Description')" required></v-textarea>
            <VTextField class="mb-4" v-model="form.duration" :label="$t('Durée (en mois)')" type="number" required></VTextField>
            <v-select class="mb-4" v-model="form.school_id" :items="schools" item-title="name" item-value="id" :label="$t('Établissement')" required></v-select>
            <v-select v-model="form.category_id" :items="categories" item-title="name" item-value="id" :label="$t('Catégorie d\'Employé')" required></v-select>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="dialog = false">{{$t('Annuler')}}</v-btn>
          <v-btn color="success" @click="submitForm">
            {{ editMode ? $t('Modifier') : $t('Créer') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from "axios";



export default {
  data() {
    return {
      formations: [],
      schools: [], // Dropdown data for schools
      categories: [], // Dropdown data for categories
      dialog: false,
      editMode: false,
      form: {
        id: null,
        name: "",
        description: "",
        duration: "", // Keep the duration in months
        school_id: null, // Selected school
        category_id: null, // Selected category
      },
      headers: [
      { title: "Nom", value: "name" },
        { title: "Description", value: "description" },
        { title: "Durée/(mois)", value: "duration" }, // Display "Durée" in months
        { title: "Établissement", value: "school.name" },
        { title: "Catégorie", value: "category.name" },
        { title: "Actions", value: "actions", sortable: false },
      ],
    };
  },
  methods: {
    fetchformations() {
      axios.get("/api/formations").then((response) => {
        this.formations = response.data;
      });
    },
    fetchSchools() {
      axios.get("/api/schools").then((response) => {
        this.schools = response.data.data || []; // Access the array in the "data" property if necessary
      });
    },
    fetchCategories() {
      axios.get("/api/categories").then((response) => {
        this.categories = response.data.data || [];
      });
    },
    openCreateDialog() {
      this.dialog = true;
      this.editMode = false;
      this.form = {
        id: null,
        name: "",
        description: "",
        duration: "", // Reset duration
        school_id: null,
        category_id: null,
      };
    },
    openEditDialog(formation) {
      this.dialog = true;
      this.editMode = true;
      this.form = { ...formation }; // Pre-fill form with selected formation data
    },
    deleteformation(id) {
      axios.delete(`/api/formations/${id}`).then(() => this.fetchformations());
    },
    submitForm() {
      const url = this.editMode
        ? `/api/formations/${this.form.id}`
        : "/api/formations";
      const method = this.editMode ? "put" : "post";

      axios[method](url, this.form).then(() => {
        this.dialog = false;
        this.fetchformations();
      });
    },
  },
  created() {
    this.fetchformations();
    this.fetchSchools();
    this.fetchCategories();
  },
};
</script>
