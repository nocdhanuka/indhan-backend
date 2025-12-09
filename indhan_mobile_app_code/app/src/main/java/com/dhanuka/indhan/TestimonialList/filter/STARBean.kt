package com.dhanuka.indhan.TestimonialList.filter


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class STARBean(
    @SerializedName("isSelected")
    var isSelected: Boolean,
    @SerializedName("rating")
    val rating: String
) : Serializable
