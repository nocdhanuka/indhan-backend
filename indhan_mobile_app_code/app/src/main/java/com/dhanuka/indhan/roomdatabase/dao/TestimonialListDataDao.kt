package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.TestimonialListData

@Dao
interface TestimonialListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: TestimonialListData?): Long

    @get:Query("SELECT * FROM TestimonialListData")
    val allTestimonialData: List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE user_ecn =:ecn")
    fun allTestimonialData(ecn: String): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE can_be_shared =:isShare AND institutionalType =:type")
    fun allTestimonialSharedData(isShare: String, type: String): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE institutional_id =:id AND institutionalType =:type")
    fun allTestimonialData(id: Int, type: String): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE institutional_id =:id")
    fun allTestimonialDataByInstitutionalId(id: Int): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE institutional_id =:id AND institutionalType =:type")
    fun allTestimonialDataByInstitutionalId(id: Int, type: String): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE institutionalType =:type")
    fun allTestimonialDataType(type: String): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE user_ecn =:ecn AND institutional_id =:id AND institutional_office_type =:type")
    fun allTestimonialData(ecn: String, id: Int, type: Int): List<TestimonialListData?>?

    @Query("SELECT * FROM TestimonialListData WHERE institutionalType =:iType")
    fun allTestimonialDataByType(iType: String): List<TestimonialListData?>?

    @Query("DELETE FROM TestimonialListData")
    fun deleteTableData()
}