package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.EvaluateRequestModel

@Dao
interface EvaluateDemoRequestDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: EvaluateRequestModel?)

    @Query("SELECT * FROM EvaluateRequestModel")
    fun getAllEvaluatedData(): List<EvaluateRequestModel?>?

    @Query("DELETE FROM EvaluateRequestModel WHERE id =:id")
    fun deleteTableData(id: Int)
}