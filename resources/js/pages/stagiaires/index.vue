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
        <v-btn icon small @click="convertToEmployee(item)">
          <v-icon>tabler-user-plus</v-icon>
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

    <!-- Dialogue pour convertir en employé -->
    <v-dialog v-model="dialogConvertToEmployee" max-width="600px">
      <v-card>
        <v-card-title>
          <span>Convertir en Employé</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="formConvertToEmployee" v-model="validConvertToEmployee">
            <v-container>
              <!-- Sélection du grade -->
              <v-select v-model="selectedGrade" :items="grades" label="Sélectionner un grade" required></v-select>

              <!-- Sélection de l'unité -->
              <v-select v-model="selectedUnite" :items="unites" label="Sélectionner une unité" required></v-select>
            </v-container>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" text @click="closeConvertToEmployee">Annuler</v-btn>
          <v-btn color="success" text @click="confirmConvertToEmployee">Confirmer</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

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
const dialogConvertToEmployee = ref(false);
const validConvertToEmployee = ref(false);
const selectedGrade = ref(null);
const selectedUnite = ref(null);
const grades = ref([]);
const unites = ref([]);
const selectedStagiaire = ref(null);

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

const fetchGradesAndUnites = async () => {
  try {
    const response = await axios.get('/api/filters');
    grades.value = response.data.grades;
    unites.value = response.data.unites;
  } catch (error) {
    console.error('Erreur lors de la récupération des grades et unités:', error);
  }
};

const convertToEmployee = async (item) => {
  selectedStagiaire.value = item;
  dialogConvertToEmployee.value = true;
};

const confirmConvertToEmployee = async () => {
  if (!validConvertToEmployee.value || !selectedStagiaire.value) return;

  const payload = {
    grade: selectedGrade.value,
    unite: selectedUnite.value,
    status: 'actif', // Changer le statut à "actif"
  };

  try {
    const response = await axios.put(
      `/api/employes/${selectedStagiaire.value.id_employe}/convert-to-employee`,
      payload
    );
    alert(response.data.message); // Afficher un message de succès
    fetchStagiaires(); // Rafraîchir la liste des stagiaires
    dialogConvertToEmployee.value = false; // Fermer le dialogue
  } catch (error) {
    console.error('Erreur lors de la conversion:', error);
    alert('Une erreur est survenue lors de la conversion.');
  }
};

const closeConvertToEmployee = () => {
  dialogConvertToEmployee.value = false;
};

// Lifecycle hook
onMounted(() => {
  fetchStagiaires();
  fetchGradesAndUnites();
});
</script>

<style scoped>
.text-right {
  text-align: end;
}
</style>
