package com.dhanuka.indhan.ATPMenu.activity.models


import androidx.room.Entity
import androidx.room.PrimaryKey
import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class ValidatedfarmerListResponse(
    @SerializedName("data")
    val `data`: List<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    @Entity(tableName = "ATPFarmer")
    data class Data(
        @SerializedName("contact_number")
        val contactNumber: String,
        @SerializedName("creation_time")
        val creationTime: String,
        @SerializedName("farmer_name")
        val farmerName: String,
        @SerializedName("invite_farmer_id")
        val inviteFarmerId: String,
        @SerializedName("plan_atp_id")
        val planAtpId: String,
        val isNewNumber: Boolean
    ) : Serializable {
        @PrimaryKey(autoGenerate = true)
        var _id: Int? = null
    }
}