package com.dhanuka.indhan.TestimonialList


import com.google.gson.annotations.SerializedName

data class TestimonialBean(
    @SerializedName("data")
    val `data`: MutableList<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("created_at")
        val createdAt: String,
        @SerializedName("crop")
        val crop: String,
        @SerializedName("crop_id")
        val cropId: String,
        @SerializedName("description")
        val description: String,
        @SerializedName("farmer")
        val farmer: String,
        @SerializedName("mobile")
        val mobile: String,
        @SerializedName("product")
        val product: String,
        @SerializedName("product_id")
        val productId: String,
        @SerializedName("self_participation_id")
        val selfParticipationId: String,
        @SerializedName("status")
        val status: String,
        @SerializedName("testimonial")
        val testimonial: String,
        @SerializedName("uploaded_at")
        val uploadedAt: Any,
        @SerializedName("uploaded_file")
        val uploadedFile: String,
        @SerializedName("user_id")
        val userId: String,
        @SerializedName("user_type")
        val userType: String,
        @SerializedName("rated")
        val rated: String,
        @SerializedName("unique_testimonial_id")
        val uniqueTestimonialId: String
    )
}
