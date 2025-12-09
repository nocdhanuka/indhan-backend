package com.dhanuka.indhan.PlanSyllabusForBdo.Dialog

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import com.dhanuka.indhan.R
import java.util.*
import kotlin.collections.ArrayList

class MultipleSelectionAdapter(
    context: Context,
    var dataSource: ArrayList<MultipleModel>,
    multipleNumberSpinner: MultipleNumberSpinner
) : BaseAdapter(), Filterable {

    private var listener: onOperation = multipleNumberSpinner
    var filterDataSource: ArrayList<MultipleModel> = dataSource
    private val inflater: LayoutInflater =
        context.getSystemService(Context.LAYOUT_INFLATER_SERVICE) as LayoutInflater

    init {
        filter
    }

    override fun getCount(): Int = filterDataSource.size

    override fun getItem(position: Int): Any {
        return filterDataSource[position]
    }

    override fun getItemId(position: Int): Long {
        return position.toLong()
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        val rowView = inflater.inflate(R.layout.multiple_item_selection, parent, false)
        val titleTextView = rowView.findViewById(R.id.txtName) as TextView
        val checkBox = rowView.findViewById(R.id.checkbox) as CheckBox
        checkBox.isChecked = filterDataSource[position].selected
        titleTextView.text = filterDataSource[position].name

        checkBox.setOnCheckedChangeListener { buttonView, isChecked ->
            filterDataSource[position].selected = !filterDataSource[position].selected
            listener.onCheckBoxClick(filterDataSource[position])
            notifyDataSetChanged()
        }

        rowView.setOnClickListener {
            filterDataSource[position].selected = !filterDataSource[position].selected
            listener.onCheckBoxClick(filterDataSource[position])
            notifyDataSetChanged()
        }
        return rowView
    }

    interface onOperation {
        fun onCheckBoxClick(data: MultipleModel)
    }

    override fun getFilter(): Filter {
        return ValueFilter()
    }

    inner class ValueFilter : Filter() {
        //Invoked in a worker thread to filter the data according to the constraint.
        override fun performFiltering(constraint: CharSequence): FilterResults {
            val results = FilterResults()
            if (constraint.isNotEmpty()) {
                val filterList: ArrayList<MultipleModel> = ArrayList()
                for (i in 0 until dataSource.size) {
                    if (dataSource[i].getName().toUpperCase(Locale.ROOT)
                            .contains(constraint.toString().toUpperCase(Locale.ROOT))
                    ) {
                        val contacts = MultipleModel()
                        contacts.setName(dataSource[i].getName())
                        contacts.setId(dataSource[i].getId())
                        filterList.add(contacts)
                    }
                }
                results.count = filterList.size
                results.values = filterList
            } else {
                results.count = filterDataSource.size
                results.values = filterDataSource
            }
            return results
        }

        //Invoked in the UI thread to publish the filtering results in the user interface.
        override fun publishResults(
            constraint: CharSequence,
            results: FilterResults
        ) {
            filterDataSource = results.values as ArrayList<MultipleModel>
            notifyDataSetChanged()
        }
    }
}