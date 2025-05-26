@extends("layouts.app")

@section("content")
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Ajouter une nouvelle visite</h4>
                </div>
                <div class="card-body">

                    @if (session("geolocation_error"))
                        <div class="alert alert-danger">
                            {{ session("geolocation_error") }}
                        </div>
                    @endif

                    <form action="{{ route("visits.store") }}" method="POST">
                        @csrf

                        <h5 class="mb-3">Détails de la visite</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="visit_date" class="form-label">Date de visite</label>
                                <input
                                    type="date"
                                    name="visit_date"
                                    id="visit_date"
                                    class="form-control @error("visit_date") is-invalid @enderror"
                                    value="{{ old("visit_date") }}" {{-- Value will be set by JS if empty --}}
                                >
                                @error("visit_date")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cleaning_type" class="form-label">Type de nettoyage</label>
                                <select name="cleaning_type" id="cleaning_type" class="form-select @error("cleaning_type") is-invalid @enderror">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($cleaningTypes as $type)
                                        <option value="{{ $type }}" {{ old("cleaning_type") == $type ? "selected" : "" }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("cleaning_type")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="location"
                                    id="location"
                                    class="form-control @error("location") is-invalid @enderror"
                                    placeholder="Entrez un lieu ou cliquez sur l'icône ->"
                                    value="{{ old("location") }}"
                                >
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="btn-current-location"
                                    title="Utiliser ma position actuelle"
                                >
                                    <i class="bi bi-geo-alt-fill"></i>
                                </button>
                            </div>
                             @error("location")
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Informations Client</h5>

                         <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Nom du client</label>
                                <input
                                    type="text"
                                    name="client_name"
                                    id="client_name"
                                    class="form-control @error("client_name") is-invalid @enderror"
                                    value="{{ old("client_name") }}"
                                >
                                @error("client_name")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="contact" class="form-label">Contact (Tél/Email)</label>
                                <input
                                    type="text"
                                    name="contact"
                                    id="contact"
                                    class="form-control @error("contact") is-invalid @enderror"
                                    value="{{ old("contact") }}"
                                >
                                @error("contact")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Suivi</h5>

                        <div class="mb-3">
                            <label for="relance_date" class="form-label">Date de relance</label>
                            <input
                                type="date"
                                name="relance_date"
                                id="relance_date"
                                class="form-control @error("relance_date") is-invalid @enderror"
                                value="{{ old("relance_date") }}"
                            >
                            @error("relance_date")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route("visits.index") }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-success">Enregistrer la visite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
{{-- Ensure SweetAlert2 JS is loaded before this script --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Autofill Visit Date ---
    const visitDateInput = document.getElementById("visit_date");
    // Set today's date only if the field is empty (e.g., not filled by old() helper)
    if (visitDateInput && !visitDateInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, "0"); // Months are 0-indexed
        const day = String(today.getDate()).padStart(2, "0");
        visitDateInput.value = `${year}-${month}-${day}`;
    }

    // --- Geolocation Logic ---
    const locationInput = document.getElementById("location");
    const locationButton = document.getElementById("btn-current-location");

    if (locationButton && locationInput) {
        locationButton.addEventListener("click", () => {
            if (!navigator.geolocation) {
                Swal.fire("Géolocalisation non supportée", "Votre navigateur ne supporte pas la géolocalisation.", "error");
                return;
            }

            Swal.fire({
                title: "Récupération de votre position…",
                text: "Veuillez patienter.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const { latitude, longitude } = position.coords;
                    try {
                        // IMPORTANT: Replace with your app name and email for Nominatim policy
                        const nominatimUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&addressdetails=1`;
                        const response = await fetch(nominatimUrl, {
                            method: "GET",
                            headers: {
                                "Accept": "application/json",
                                "User-Agent": "VotreNomApp/1.0 votre-email@example.com" // <-- **MODIFIEZ CECI**
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Erreur Nominatim: ${response.statusText}`);
                        }
                        const data = await response.json();
                        let address = data.display_name || (
                            data.address ?
                            [data.address.road, data.address.house_number, data.address.postcode, data.address.city, data.address.country].filter(Boolean).join(", ")
                            : `${latitude}, ${longitude}`
                        );
                        locationInput.value = address;
                        Swal.close();
                    } catch (error) {
                        console.error("Erreur lors du géocodage inversé:", error);
                        Swal.fire("Erreur", "Impossible de récupérer l'adresse correspondant à votre position.", "error");
                    }
                },
                (error) => {
                    let errorMessage = "Une erreur inconnue est survenue lors de la récupération de votre position.";
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = "Vous avez refusé la permission de géolocalisation. Veuillez l'autoriser dans les paramètres de votre navigateur.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = "Votre position actuelle n'est pas disponible.";
                            break;
                        case error.TIMEOUT:
                            errorMessage = "La demande de géolocalisation a expiré.";
                            break;
                    }
                    console.error("Erreur de géolocalisation:", error.message);
                    Swal.fire("Erreur de géolocalisation", errorMessage, "error");
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    } else {
        console.error("Impossible de trouver les éléments #location ou #btn-current-location");
    }

    // Log if visit date input is missing (for debugging)
    if (!visitDateInput) {
         console.error("Impossible de trouver l'élément #visit_date");
    }
});
</script>
@endpush

