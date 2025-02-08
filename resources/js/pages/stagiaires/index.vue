<template>
  <v-container>
    <v-row justify="space-between" align="center">
      <v-col cols="8">
        <h1>Gestion des Stagiaires</h1>
      </v-col>
      <v-col cols="4" class="text-right">
        <!-- <v-btn color="primary" @click="openDialog()">
          Ajouter un Stagiaire
        </v-btn> -->
      </v-col>
    </v-row>

    <!-- Search and Filters -->
    <v-row>
      <v-col cols="4">
        <v-text-field v-model="search" label="Rechercher" prepend-icon="mdi-magnify" clearable
          @input="fetchStagiaires"></v-text-field>
      </v-col>
    </v-row>

    <!-- Table des stagiaires -->
    <v-data-table :headers="headers" :items="stagiaires" :loading="loading" :search="search" class="elevation-1">
      <template v-slot:item.actions="{ item }">
        <v-btn icon small @click="openDialog(item)">
          <v-icon>tabler-pencil</v-icon>
        </v-btn>
        <v-btn icon small @click="exportSinglePDF(item)">
          <v-icon>tabler-pdf</v-icon>
        </v-btn>
        <v-btn icon small @click="confirmDelete(item)">
          <v-icon>tabler-trash</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Dialog pour ajouter/modifier -->
    <v-dialog v-model="dialog" max-width="1200px">
      <v-card>
        <v-card-title>
          <span>{{ formTitle }}</span>
        </v-card-title>

        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-container>
              <!-- Formulaire pour ajouter/modifier un stagiaire -->
              <!-- (Copiez le formulaire existant ici) -->
            </v-container>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" text @click="close">Annuler</v-btn>
          <v-btn color="success" text @click="save">Sauvegarder</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog de confirmation de suppression -->
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title>Êtes-vous sûr de vouloir supprimer ce Stagiaire ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="deleteItem">Confirmer</v-btn>
          <v-btn color="primary" text @click="closeDelete">Annuler</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';

// Réactifs
const search = ref('');
const stagiaires = ref([]);
const headers = ref([
  { title: 'Nom', key: 'nom' },
  { title: 'Prénom', key: 'prenom' },
  { title: 'Matricule', key: 'matricule' },
  { title: 'Grade', key: 'grade' },
  { title: 'Unité', key: 'unite' },
  { title: 'Statut', key: 'status' },
  { title: 'Actions', key: 'actions', sortable: false },
]);
const loading = ref(false);

// Méthodes
const fetchStagiaires = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/employes', {
      params: {
        status: 'stage', // Filtrer par statut "stage"
      },
    });
    stagiaires.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des stagiaires:', error);
  } finally {
    loading.value = false;
  }
};

// Lifecycle hook
onMounted(() => {
  fetchStagiaires();
});
</script>

<style scoped>
.text-right {
  text-align: end;
}
</style>
