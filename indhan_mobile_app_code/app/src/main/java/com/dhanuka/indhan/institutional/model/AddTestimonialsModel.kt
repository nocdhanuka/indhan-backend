package com.dhanuka.indhan.institutional.model

data class AddTestimonialsModel(
    var id: Long,
    var testimonialId: String,
    var testimonialLink: String,
    var testimonialName: String,
    var testimonialMessage: String
)