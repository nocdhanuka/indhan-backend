package com.dhanuka.indhan.ATPMenu.activity.models

import com.google.gson.annotations.SerializedName

data class InviteFarmerList(
    @SerializedName("contact_number")
    val contactNumber: String,
    @SerializedName("farmer_name")
    val farmerName: String
)