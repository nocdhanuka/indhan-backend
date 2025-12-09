package com.dhanuka.indhan.ATPMenu.activity.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Filter
import android.widget.Filterable
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestData
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.item_choose_demo.view.*

class DemoChooseAdapter(
    context: Context,
    demoList: ArrayList<DemoRequestData>,
    listener: OnSuccess
) : RecyclerView.Adapter<DemoChooseAdapter.ViewHolder>(), Filterable {
    override fun getFilter(): Filter {
        return object : Filter() {
            override fun performFiltering(constraint: CharSequence?): FilterResults {
                var constraintTemp = constraint
                //here constraint charSequence which is type in search bar
                val results = FilterResults()
                val FilteredArrList = mutableListOf<DemoRequestData>()
                if (demoListTwo.size == 0) {
                    demoListTwo.addAll(demoList)
                    // saves the original data in contactDataListOriginal

                }
                if (constraint == null || constraint.isEmpty()) {

                    // set the Original result to return
                    results.count = demoListTwo.size
                    results.values = demoListTwo
                } else {
//                    search = constraint as String?
                    constraintTemp = constraintTemp.toString().toLowerCase()
                    for (i in demoListTwo.indices) {
                        val data = demoListTwo[i]

                        if (data.demo_request_id.toString().toLowerCase()
                                .startsWith(constraintTemp.toString())
                        ) {
                            FilteredArrList.add(data)
                        }

                    }
                    // set the Filtered result to return
                    results.count = FilteredArrList.size
                    results.values = FilteredArrList
                }
                return results
            }

            override fun publishResults(constraint: CharSequence, results: FilterResults?) {
                demoList.clear()
                demoList.addAll(results?.values as ArrayList<DemoRequestData>)
                notifyDataSetChanged()


//                if (results != null && results.count == 0) {
//                    txtView.setVisibility(View.VISIBLE)
//                    txtView.setText("No results found for '$search'")
//                } else {
//                    txtView.setVisibility(View.GONE)
//                }
            }
        }
    }

    var context: Context
    var demoList: ArrayList<DemoRequestData>
    var demoListTwo = mutableListOf<DemoRequestData>()
    private var search: String? = null


    var listener: OnSuccess
    var oldPosition = -1


    init {
        this.context = context
        this.demoList = demoList
        this.listener = listener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_choose_demo, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return demoList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtCropName.text = demoList[position].crop
        holder.view.productName.text = demoList[position].product
        holder.view.txtFarmerName.text = demoList[position].farmer_name
        holder.view.txtDemoId.text = "Demo ID: " + demoList[position].demo_code
        holder.view.txtVillage.text = demoList[position].village
        holder.view.txtStatus.text = "Status: " + demoList[position].status


        //for highlight selected item
        if (oldPosition == position) {
            holder.view.llProductDetail.setBackgroundResource(R.drawable.bg_selection)
        } else {
            holder.view.llProductDetail.setBackgroundResource(R.drawable.bg_edit)
        }


        holder.view.llProductDetail.setOnClickListener {
            listener.onClick("" + demoList[position].demo_request_id)
            //high light selected item
            oldPosition = position
            notifyDataSetChanged()
        }


    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }
}