package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownData

@Dao
interface GoDownDataDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: GoDownData)

    @Query("SELECT * FROM GoDownData")
    fun getAllGoDownData(): List<GoDownData?>?

    @Query("SELECT * FROM GoDownData WHERE district_id =:district_id")
    fun getAllGoDownDataForDistrict(district_id: Int): List<GoDownData?>?

    @Query("DELETE FROM GoDownData WHERE godown_id =:id")
    fun deleteTableData(id: Int)
}