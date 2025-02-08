<template>
  <v-container>
    <v-row justify="space-between" align="center">
      <v-col cols="8">
        <h1>Gestion des Employees</h1>
      </v-col>
      <v-col cols="4" class="text-right">
        <v-btn color="primary" @click="openDialog()">
          Ajouter un Employe
        </v-btn>
      </v-col>
    </v-row>

    <!-- Search and Filters -->
    <v-row>
      <v-col cols="4">
        <v-text-field v-model="search" label="Rechercher" prepend-icon="mdi-magnify" clearable
          @input="fetchEmployes"></v-text-field>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedGrade" :items="grades" label="Filtrer par grade" clearable
          @change="fetchEmployes"></v-select>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedUnite" :items="unites" label="Filtrer par unité" clearable
          @change="fetchEmployes"></v-select>
      </v-col>
      <v-col cols="2">
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" v-bind="attrs" v-on="on">
              Exporter
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="exportData('excel')">
              <v-list-item-title>Exporter en Excel</v-list-item-title>
            </v-list-item>
            <v-list-item @click="exportData('pdf')">
              <v-list-item-title>Exporter en PDF</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <!-- Table des employés -->
    <v-data-table :headers="headers" :items="employes" :loading="loading" :search="search" class="elevation-1">
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
        <!-- <v-btn icon small @click="convertToStage(item)">
          <v-icon>tabler-user-plus</v-icon>
        </v-btn> -->
        <v-btn icon small @click="openConvertToStage(item)">
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
              <v-row>
                <!-- Informations Personnelles -->
                <v-col cols="12">
                  <h3>Informations Personnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nom" label="Nom" :rules="[v => !!v || 'Le nom est requis']"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.prenom" label="Prénom" :rules="[v => !!v || 'Le prénom est requis']"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.sexe" :items="sexOptions" label="Sexe" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_naissance" label="Date de naissance" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.lieu_de_naissance" label="Lieu de naissance"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.pays" label="Pays" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nationalite" label="Nationalité" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.adresse_actuelle" label="Adresse actuelle" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.situation_familiale" :items="situationFamilialeOptions"
                    label="Situation familiale" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.numero_telephone" label="Numéro de téléphone"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.niveau_scolaire" :items="niveauScolaireOptions" label="Niveau scolaire"
                    required></v-select>
                </v-col>

                <!-- Informations Professionnelles -->
                <v-col cols="12">
                  <h3>Informations Professionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.grade" :items="gradeOptions" label="Grade" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.matricule" label="Matricule" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.unite" label="Unité" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nom_entreprise" label="Nom de l'entreprise" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_recrutement" label="Date de recrutement" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.mode_engagement" :items="modeEngagementOptions"
                    label="Mode d'engagement" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_mise_a_niveau" label="Date de mise à niveau" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_nomination_active" label="Date de nomination active"
                    type="date" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.fonction_actuelle" label="Fonction actuelle"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.annee_scolaire" :items="anneeScolaireOptions" label="Année scolaire"
                    required></v-select>
                </v-col>

                <!-- Formation -->
                <v-col cols="12">
                  <h3>Formation</h3>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.type_formation" :items="typeFormationOptions"
                    label="Type de formation" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.nature_diplome" :items="natureDiplomeOptions" label="Nature du diplôme"
                    required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.specialite" :items="specialiteOptions" label="Spécialité" multiple
                    chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.specialite_partielle" :items="specialitePartielleOptions"
                    label="Spécialité partielle" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.lieu_formation" label="Lieu de formation" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.annee_formation" :items="anneeFormationOptions"
                    label="Année de formation" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.duree_formation" label="Durée de formation" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.diplome_obtenu" :items="diplomeObtenuOptions" label="Diplôme obtenu"
                    multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.formations_precedentes" :items="formationsPrecedentesOptions"
                    label="Formations précédentes" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_obtention_diplome" label="Date d'obtention du diplôme"
                    type="date" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.diplomes_precedents" :items="diplomesPrecedentsOptions"
                    label="Diplômes précédents" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.autres_diplomes" :items="autresDiplomesOptions"
                    label="Autres diplômes" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.cycle" :items="cycleOptions" label="Cycle" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_debut_formation" label="Date de début de formation" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_fin_formation" label="Date de fin de formation" type="date"
                    required></v-text-field>
                </v-col>

                <!-- Informations Additionnelles -->
                <v-col cols="12">
                  <h3>Informations Additionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.punition" :items="punitionOptions" label="Punition" multiple
                    chips></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.convalescences" :items="convalescencesOptions" label="Convalescences"
                    multiple chips></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.etat_sante" :items="etatSanteOptions" label="État de santé"></v-select>
                </v-col>
              </v-row>
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
        <v-card-title>Êtes-vous sûr de vouloir supprimer cet Employe ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="deleteItem">Confirmer</v-btn>
          <v-btn color="primary" text @click="closeDelete">Annuler</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialogue pour convertir en stage -->
    <v-dialog v-model="dialogConvertToStage" max-width="600px">
      <v-card>
        <v-card-title>
          <span>Convertir en Stage</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="formConvertToStage" v-model="validConvertToStage">
            <v-container>
              <!-- Sélection de l'école -->
              <v-select v-model="selectedSchool" :items="schools" item-title="name" item-value="id"
                label="Sélectionner une école" required @update:modelValue="fetchFormations"></v-select>

              <!-- Sélection de la formation -->
              <v-select v-model="selectedFormation" :items="formations" item-title="name" item-value="id"
                label="Sélectionner une formation" :disabled="!selectedSchool" required></v-select>

              <!-- Durée du stage (en mois) -->
              <v-text-field v-model="stageDuration" label="Durée du stage (en mois)" type="number" min="1"
                required></v-text-field>
            </v-container>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" text @click="closeConvertToStage">Annuler</v-btn>
          <v-btn color="success" text @click="confirmConvertToStage">Confirmer</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';

// Réactifs pour la gestion des employés
const search = ref('');
const selectedGrade = ref(null);
const selectedUnite = ref(null);
const grades = ref([]);
const unites = ref([]);
const employes = ref([]);
const editedItem = ref({});
const dialog = ref(false);
const dialogDelete = ref(false);
const formTitle = ref('');
const loading = ref(false);
const valid = ref(false);

// Réactifs pour le dialogue de conversion en stage
const dialogConvertToStage = ref(false);
const validConvertToStage = ref(false);
const selectedSchool = ref(null);
const selectedFormation = ref(null);
const stageDuration = ref(null);
const schools = ref([]);
const formations = ref([]);
const selectedEmployee = ref(null);

// Options pour les menus déroulants
const sexOptions = ['Homme', 'Femme'];
const situationFamilialeOptions = ['célibataire', 'Marié'];
const niveauScolaireOptions = ['1ère', '2ème', '3ème', '4ème', '5ème'];
const gradeOptions = ['agent', 'agent régional', 'Comn', 'capi'];
const modeEngagementOptions = ['par voie directe', 'recrutement sur titre', 'direct', 'conversion'];
const anneeScolaireOptions = ['1ère année', '2ème année', '3ème année', '4ème année'];
const typeFormationOptions = ['informatique', 'musique', 'sport', 'presse'];
const natureDiplomeOptions = ['civil', 'mili'];
const specialiteOptions = ['web', 'mobile', 'sport', 'presse'];
const specialitePartielleOptions = ['Dev web mobile', 'Dev web', 'sport', 'presse'];
const formationsPrecedentesOptions = ['تكوين في إدارة', 'Dev web', 'تكوين شهادة في الرياضة'];
const diplomesPrecedentsOptions = ['diplôme Dev web mobile', 'diplôme finance', 'diplôme électricien'];
const autresDiplomesOptions = ['شهادة في الغرف', 'شهادة في الكرم'];
const cycleOptions = ['1ère session', '2ème session'];
const punitionOptions = ['15 JOUR', '30 JOUR'];
const convalescencesOptions = ['15 JOUR', '30 JOUR'];
const etatSanteOptions = ['مؤهل', 'غير مؤهل'];
const diplomeObtenuOptions = ['technicien supérieur', 'licence', 'master'];
const anneeFormationOptions = ['2022/2023', '2023/2024', '2024/2025'];

// En-têtes de la table
const headers = [
  { title: 'Nom', key: 'nom' },
  { title: 'Prénom', key: 'prenom' },
  { title: 'Matricule', key: 'matricule' },
  { title: 'Grade', key: 'grade' },
  { title: 'Unité', key: 'unite' },
  { title: 'Actions', key: 'actions', sortable: false },
];

// Fonction pour récupérer la liste des employés
const fetchEmployes = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/employes', {
      params: {
        status: 'actif', // Filtrer par statut "stage"
      },
    });
    employes.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des employés:', error);
    alert('Une erreur est survenue lors de la récupération des employés.');
  } finally {
    loading.value = false;
  }
};

// Fonction pour récupérer les grades et unités
const fetchGradesAndUnites = async () => {
  try {
    const response = await axios.get('/api/filters');
    grades.value = response.data.grades;
    unites.value = response.data.unites;
  } catch (error) {
    console.error('Erreur lors de la récupération des filtres:', error);
  }
};

// Ouvrir le dialogue pour ajouter/modifier un employé
const openDialog = (item = null) => {
  editedItem.value = item ? { ...item } : {};
  formTitle.value = item ? 'Modifier un employe' : 'Ajouter un employe';
  dialog.value = true;
};

// Sauvegarder un employé
const save = async () => {
  if (!valid.value) return;

  const payload = {
    ...editedItem.value,
    type_formation: JSON.stringify(editedItem.value.type_formation || []),
    specialite: JSON.stringify(editedItem.value.specialite || []),
    specialite_partielle: JSON.stringify(editedItem.value.specialite_partielle || []),
    formations_precedentes: JSON.stringify(editedItem.value.formations_precedentes || []),
    diplomes_precedents: JSON.stringify(editedItem.value.diplomes_precedents || []),
    autres_diplomes: JSON.stringify(editedItem.value.autres_diplomes || []),
    punition: JSON.stringify(editedItem.value.punition || []),
    convalescences: JSON.stringify(editedItem.value.convalescences || []),
    annee_formation: JSON.stringify(editedItem.value.annee_formation || []),
    diplome_obtenu: JSON.stringify(editedItem.value.diplome_obtenu || []),
  };

  try {
    if (editedItem.value.id_employe) {
      await axios.put(`/api/employes/${editedItem.value.id_employe}`, payload);
    } else {
      await axios.post('/api/employes', payload);
    }
    fetchEmployes();
    dialog.value = false;
  } catch (error) {
    console.error('Erreur lors de la sauvegarde de l\'employé:', error);
  }
};

// Confirmer la suppression d'un employé
const confirmDelete = (item) => {
  editedItem.value = { ...item };
  dialogDelete.value = true;
};

// Supprimer un employé
const deleteItem = async () => {
  try {
    await axios.delete(`/api/employes/${editedItem.value.id_employe}`);
    fetchEmployes();
    dialogDelete.value = false;
  } catch (error) {
    console.error('Erreur lors de la suppression de l\'employé:', error);
  }
};

// Exporter les données
const exportData = async (type) => {
  try {
    const response = await axios.get(`/api/employes/export/${type}`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `employees.${type}`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error('Erreur lors de l\'exportation des données:', error);
  }
};

// Exporter un employé en PDF
const exportSinglePDF = async (item) => {
  try {
    const response = await axios.get(`/api/employes/${item.id_employe}/export`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${item.nom}_${item.prenom}.pdf`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error('Erreur lors de l\'exportation du PDF:', error);
  }
};

// Fermer le dialogue
const close = () => {
  dialog.value = false;
};

// Fermer le dialogue de suppression
const closeDelete = () => {
  dialogDelete.value = false;
};

const openConvertToStage = async (item) => {
  if (!item?.id_employe) {
    alert('ID de l\'employé non trouvé.');
    return;
  }

  // Stocker l'employé sélectionné
  selectedEmployee.value = item;

  // Réinitialiser les sélections
  selectedSchool.value = null;
  selectedFormation.value = null;
  stageDuration.value = null;

  // Charger les écoles
  try {
    const response = await axios.get('/api/schools');
    schools.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des écoles:', error);
    alert('Une erreur est survenue lors du chargement des écoles.');
  }

  // Ouvrir le dialogue
  dialogConvertToStage.value = true;
};
// Charger les écoles
const fetchSchools = async () => {
  try {
    const response = await axios.get('/api/schools');
    schools.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des écoles:', error);
    alert('Une erreur est survenue lors du chargement des écoles.');
  }
};

// Charger les formations en fonction de l'école sélectionnée
const fetchFormations = async () => {
  if (!selectedSchool.value) return;

  try {
    const response = await axios.get('/api/formations', {
      params: {
        school_id: selectedSchool.value,
      },
    });
    formations.value = response.data;
  } catch (error) {
    console.error('Erreur lors du chargement des formations:', error);
    alert('Une erreur est survenue lors du chargement des formations.');
  }
};

// Confirmer la conversion en stage
const confirmConvertToStage = async () => {
  if (!validConvertToStage.value || !selectedEmployee.value) return;

  const payload = {
    school_id: selectedSchool.value,
    formation_id: selectedFormation.value,
    duration: stageDuration.value,
  };

  try {
    const response = await axios.post(
      `/api/employes/${selectedEmployee.value.id_employe}/convert-to-stage`,
      payload
    );
    alert(response.data.message);
    fetchEmployes();
    dialogConvertToStage.value = false;
  } catch (error) {
    console.error('Erreur lors de la conversion:', error);
    alert('Une erreur est survenue lors de la conversion.');
  }
};

// Fermer le dialogue de conversion en stage
const closeConvertToStage = () => {
  dialogConvertToStage.value = false;
};

// Convertir un employé en stage
const convertToStage = async (item) => {
  if (!item?.id_employe) {
    alert('ID de l\'employé non trouvé.');
    return;
  }

  if (confirm('Êtes-vous sûr de vouloir convertir cet employé en stage ?')) {
    try {
      const response = await axios.post(`/api/employes/${item.id_employe}/convert-to-stage`);
      alert(response.data.message);
      fetchEmployes();
    } catch (error) {
      console.error('Erreur lors de la conversion:', error);
      alert('Une erreur est survenue lors de la conversion.');
    }
  }
};

// Charger les données au montage du composant
fetchEmployes();
fetchGradesAndUnites();
</script>
<style scoped>
.text-right {
  text-align: end;
}
</style>
