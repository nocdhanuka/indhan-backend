package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData

@Dao
interface RequestedMaterialListDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: RequestedMaterialResponseData)

    @Query("SELECT * FROM RequestedMaterialResponseData")
    fun getAllMaterialRequestedData(): List<RequestedMaterialResponseData?>?

    @Query("DELETE FROM RequestedMaterialResponseData WHERE demo_material_request_id =:id")
    fun deleteTableData(id: Int)
}