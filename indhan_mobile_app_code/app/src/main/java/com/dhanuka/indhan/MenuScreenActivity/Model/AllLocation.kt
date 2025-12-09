package com.dhanuka.indhan.MenuScreenActivity.Model


import com.google.gson.annotations.SerializedName

data class AllLocation(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String, // All Locations.
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Data(
        @SerializedName("districts")
        val districts: List<District>,
        @SerializedName("states")
        val states: List<State>,
        @SerializedName("tehsils")
        val tehsils: List<Tehsil>,
        @SerializedName("villages")
        val villages: List<Village>
    ) {
        data class Village(
            @SerializedName("district_id")
            val districtId: String, // 1015
            @SerializedName("tehsil_id")
            val tehsilId: String, // 8864
            @SerializedName("village")
            val village: String, // Lamachor
            @SerializedName("village_id")
            val villageId: String // 3
        )

        data class State(
            @SerializedName("state")
            val state: String, // Andhra Pradesh
            @SerializedName("state_id")
            val stateId: String // 2
        )

        data class Tehsil(
            @SerializedName("district_id")
            val districtId: String, // 1
            @SerializedName("tehsil")
            val tehsil: String, // Great Nicobar
            @SerializedName("tehsil_id")
            val tehsilId: String // 2
        )

        data class District(
            @SerializedName("district")
            val district: String, // North And Middle Andaman
            @SerializedName("district_id")
            val districtId: String, // 2
            @SerializedName("state_id")
            val stateId: String // 1
        )
    }
}