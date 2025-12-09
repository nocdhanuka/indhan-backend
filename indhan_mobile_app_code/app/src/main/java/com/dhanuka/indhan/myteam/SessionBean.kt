package com.dhanuka.indhan.myteam


import com.google.gson.annotations.SerializedName

data class SessionBean(
    @SerializedName("data")
    val `data`: List<Data>,
    @SerializedName("message")
    val message: String, // Seasons List Found!
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Data(
        @SerializedName("id")
        val id: String, // 3
        @SerializedName("season_name")
        val seasonName: String // Winter Season
    )
}