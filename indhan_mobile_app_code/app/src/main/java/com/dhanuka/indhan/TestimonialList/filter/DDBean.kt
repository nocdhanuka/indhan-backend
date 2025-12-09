package com.dhanuka.indhan.TestimonialList.filter


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class DDBean(
    @SerializedName("data")
    val `data`: MutableList<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) : Serializable {
    data class Data(
        @SerializedName("isSelected")
        var isSelected: Boolean,
        @SerializedName("app_permission_close_ticket")
        val appPermissionCloseTicket: String,
        @SerializedName("app_permission_edit_ticket")
        val appPermissionEditTicket: String,
        @SerializedName("averageFarmer")
        val averageFarmer: Double,
        @SerializedName("cropCount")
        val cropCount: Int,
        @SerializedName("dd_address")
        val ddAddress: String,
        @SerializedName("dd_alt_mobile_no")
        val ddAltMobileNo: String,
        @SerializedName("dd_code")
        val ddCode: String,
        @SerializedName("dd_email")
        val ddEmail: String,
        @SerializedName("dd_hq")
        val ddHq: String,
        @SerializedName("dd_id")
        val ddId: String,
        @SerializedName("dd_image")
        var ddImage: String,
        @SerializedName("dd_mobile_no")
        val ddMobileNo: String,
        @SerializedName("department")
        val department: String,
        @SerializedName("designation")
        val designation: String,
        @SerializedName("district")
        val district: String,
        @SerializedName("ECN")
        val eCN: String,
        @SerializedName("language")
        val language: String,
        @SerializedName("name")
        val name: String,
        @SerializedName("state")
        val state: String,
        @SerializedName("tehsil")
        val tehsil: String,
        @SerializedName("villagesCount")
        val villagesCount: Int
    ) : Serializable
}