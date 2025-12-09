package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData

@Dao
interface InfluencersTestimonialsDataListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencerTestimonialData?): Long

    @Query("SELECT * FROM InfluencerTestimonialData WHERE testimonial =:type")
    fun allTestimonialData(type: String): List<InfluencerTestimonialData?>?

    @Query("DELETE FROM InfluencerTestimonialData")
    fun deleteTableData()
}