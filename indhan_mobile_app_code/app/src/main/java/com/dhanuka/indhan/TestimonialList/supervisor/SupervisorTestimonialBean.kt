package com.dhanuka.indhan.TestimonialList.supervisor


import com.google.gson.annotations.SerializedName

data class SupervisorTestimonialBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String, // Testimonial list.
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Data(
        @SerializedName("testimonialData")
        val testimonialData: MutableList<TestimonialData>,
        @SerializedName("totalBd_flTestimonials")
        val totalBdFlTestimonials: Int, // 0
        @SerializedName("totalS_flTestimonials")
        val totalSFlTestimonials: Int, // 0
        @SerializedName("totalTestimonials")
        val totalTestimonials: Int // 611
    ) {
        data class TestimonialData(
            @SerializedName("created_at")
            val createdAt: String, // 2019-08-19 16:34:53
            @SerializedName("rated")
            var rated: String, // 2019-08-19 16:34:53
            @SerializedName("JuniorRated")
            var JuniorRated: String, // 2019-08-19 16:34:53
            @SerializedName("crop")
            val crop: String, // Carrot
            @SerializedName("crop_id")
            val cropId: String, // Carrot
            @SerializedName("crop_ids")
            val cropIds: String, // 12
            @SerializedName("description")
            val description: String, // test
            @SerializedName("farmer")
            val farmer: String, // testdeep
            @SerializedName("mobile")
            val mobile: String, // 9717975470
            @SerializedName("product")
            val product: String, // Maxyld
            @SerializedName("product_id")
            val productId: String, // Maxyld
            @SerializedName("product_ids")
            val productIds: String, // 43
            @SerializedName("self_participation_id")
            val selfParticipationId: String, // 6384
            @SerializedName("status")
            val status: String, // 1
            @SerializedName("testimonial")
            val testimonial: String, // Kohinoor
            @SerializedName("testimonial_rating")
            val testimonialRating: MutableList<TestimonialRating>,
            @SerializedName("unique_testimonial_id")
            val uniqueTestimonialId: String, // T9063
            @SerializedName("uploaded_at")
            val uploadedAt: String,
            @SerializedName("uploaded_file")
            val uploadedFile: String, // http://14.142.56.172:8088/DhanukaV2dev/assets/testimonials/T90787a3ea2dbf1494c04d77abf56d863.mp4
            @SerializedName("user_id")
            val userId: String, // 2
            @SerializedName("user_type")
            val userType: String,
            @SerializedName("uploader_name")
            val uploader_name: String,
            @SerializedName("ratedAlready")
            var ratedAlready: Boolean = false  // 6384
        ) {
            data class TestimonialRating(
                @SerializedName("created_at")
                val createdAt: String, // 2019-09-12 23:36:46
                @SerializedName("rating")
                val rating: String, // 3
                @SerializedName("supervisor_id")
                val supervisorId: String, // 1
                @SerializedName("supervisor_name")
                val supervisorName: String, // deep ram
                @SerializedName("supervisor_type")
                val supervisorType: String, // marketing_supervisor
                @SerializedName("testimonial_id")
                val testimonialId: String,
                @SerializedName("ecn")
                val ecn: String

            )
        }
    }
}