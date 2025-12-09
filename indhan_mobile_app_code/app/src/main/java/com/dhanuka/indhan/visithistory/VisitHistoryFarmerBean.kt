package com.dhanuka.indhan.visithistory

import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.TypeConverters
import com.dhanuka.indhan.roomdatabase.convertors.VisitHistoryFarmerConvertor
import com.google.gson.annotations.SerializedName

data class VisitHistoryFarmerBean(
    @SerializedName("data")
    var `data`: MutableList<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    @Entity(tableName = "VisitHistoryFarmer")
    data class Data(
        @SerializedName("call_status")
        var callStatus: String,
        @SerializedName("created_at")
        val createdAt: String,
        @SerializedName("createdDate")
        val createdDate: String,
        @SerializedName("farmer_id")
        val farmerId: String,
        @SerializedName("farmerName")
        val farmerName: String,
        @TypeConverters(VisitHistoryFarmerConvertor::class)
        @SerializedName("farmerProducts")
        val farmerProducts: MutableList<FarmerProduct>,
        @SerializedName("lead_status")
        var leadStatus: String,
        @PrimaryKey
        @SerializedName("markVisitId")
        val markVisitId: String,
        @SerializedName("village")
        val village: String?,
        @SerializedName("visitImage")
        val visitImage: String,
        @SerializedName("contact_number")
        val contact_number: String,
        @SerializedName("crop")
        val crop: String
    ) {
        data class FarmerProduct(
            @SerializedName("product")
            val product: String,
            @SerializedName("product_id")
            val productId: String
        )
    }
}